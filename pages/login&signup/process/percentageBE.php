<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("../../assets/shared/connect.php");

// Check for logged-in user
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}

$userID = $_SESSION['userID'];

// Currency setup (from session)
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';

$categories = [];
$savingsCategory = null;
$error = '';
$show_tracking_prompt = false;

function clean_currency_input($value)
{
    return trim(preg_replace('/[^0-9.]/', '', $value));
}

/* ----------------------------------------------------------
   FETCH EXPENSE CATEGORIES
----------------------------------------------------------- */
$sql_categories = "
    SELECT userCategoryID, categoryName, userisFlexible, userNecessityType 
    FROM tbl_usercategories 
    WHERE userID = ? AND type = 'expense' AND isSelected = 1
    ORDER BY userNecessityType DESC, categoryName ASC
";
$stmt_categories = $conn->prepare($sql_categories);
$stmt_categories->bind_param("i", $userID);
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();

while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row;
}
$stmt_categories->close();

/* ----------------------------------------------------------
   FETCH SAVINGS CATEGORY
----------------------------------------------------------- */
$sql_savings = "
    SELECT userCategoryID, categoryName 
    FROM tbl_usercategories 
    WHERE userID = ? AND type = 'savings' AND categoryName = 'Savings'
";
$stmt_savings = $conn->prepare($sql_savings);
$stmt_savings->bind_param("i", $userID);
$stmt_savings->execute();
$savingsCategory = $stmt_savings->get_result()->fetch_assoc();
$stmt_savings->close();

if (!$savingsCategory) {
    $error = "CRITICAL ERROR: 'Savings' category not found.";
}

/* ----------------------------------------------------------
   FETCH TOTAL INCOME (for validation)
----------------------------------------------------------- */
$totalIncome = 0.00;

$sql_income = "
    SELECT totalIncome
    FROM tbl_userbudgetversion
    WHERE userID = ? AND isActive = 1
    ORDER BY userBudgetversionID DESC LIMIT 1
";

$stmt_income = $conn->prepare($sql_income);
$stmt_income->bind_param("i", $userID);
$stmt_income->execute();
$row = $stmt_income->get_result()->fetch_assoc();
if ($row) {
    $totalIncome = (float)$row['totalIncome'];
}
$stmt_income->close();

echo "<script>window.userIncome = {$totalIncome};</script>";

/* ----------------------------------------------------------
   HANDLE POST REQUEST
----------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {

    $all_tracking = true;
    $valid_allocation = true;
    $allocations = [];
    $total_limit_count = 0;

    /* -----------------------------
        PROCESS EXPENSE CATEGORIES
    ------------------------------ */
    foreach ($categories as $category) {

        $id = $category['userCategoryID'];
        $base = "category_" . $id;

        $mode = $_POST[$base . "_mode"] ?? '';
        $value = trim($_POST[$base . "_value"] ?? '');
        $clean = clean_currency_input($value);

        if (!$mode) {
            $valid_allocation = false;
            $error = "Missing mode for: " . htmlspecialchars($category['categoryName']);
            break;
        }

        $allocation = [
            'userCategoryID' => $id,
            'necessityType' => $category['userNecessityType'],
            'limitType' => ($mode === 'limit') ? 1 : 0,
            'value' => 0.00
        ];

        if ($mode === 'limit') {
            $all_tracking = false;
            $total_limit_count++;

            if (empty($value) || !is_numeric($clean) || (float)$clean <= 0) {
                $valid_allocation = false;
                $error = "Limit for {$category['categoryName']} must be positive.";
                break;
            }

            $allocation['value'] = (float)$clean;
        }

        $allocations[] = $allocation;
    }

    /* -----------------------------
        PROCESS SAVINGS (ALWAYS REQUIRED)
    ------------------------------ */
    if ($valid_allocation) {

        $savings_value = trim($_POST['savings_value'] ?? '');
        $clean_savings = clean_currency_input($savings_value);

        if (empty($savings_value) || !is_numeric($clean_savings) || (float)$clean_savings <= 0) {
            $valid_allocation = false;
            $error = "Savings must be a positive percentage.";
        } else {
            $allocations[] = [
                'userCategoryID' => $savingsCategory['userCategoryID'],
                'necessityType' => 'saving',
                'limitType' => 1,
                'value' => (float)$clean_savings
            ];
            $total_limit_count++;
        }
    }

    /* -----------------------------
        OPTIONAL TRACKING WARNING
    ------------------------------ */
    if ($valid_allocation && $all_tracking && $total_limit_count == 1) {
        if (!isset($_POST['tracking_confirm'])) {
            $show_tracking_prompt = true;
            $valid_allocation = false;
        }
    }

    /* -----------------------------
        VALIDATE TOTAL LIMITS
    ------------------------------ */
    if ($valid_allocation && !$show_tracking_prompt) {

        if ($totalIncome <= 0) {
            $valid_allocation = false;
            $error = "Set your income before creating limits.";
        } else {
            $sumLimits = 0.00;

            foreach ($allocations as $a) {
                if ($a['limitType'] == 1) {
                    $sumLimits += $a['value'];
                }
            }

            if ($sumLimits > $totalIncome) {
                $valid_allocation = false;
                $error = "Your limit totals exceed 100% of your income.";
            }
        }
    }

    /* -----------------------------
        SAVE CUSTOM RULE
    ------------------------------ */
    if ($valid_allocation && !$show_tracking_prompt) {

        $conn->begin_transaction();

        try {

            // Unselect old rules
            $stmt = $conn->prepare("UPDATE tbl_userbudgetrule SET isSelected = 0 WHERE userID = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->close();

            // Create NEW custom rule
            $ruleName = "Custom-Rule";
            $stmt = $conn->prepare("
                INSERT INTO tbl_userbudgetrule (userID, ruleName, isSelected)
                VALUES (?, ?, 1)
            ");
            $stmt->bind_param("is", $userID, $ruleName);
            $stmt->execute();
            $ruleID = $stmt->insert_id;
            $stmt->close();

            // -------------------------------
            // ⭐ CRITICAL FIX:
            // Attach custom rule to active budget version
            // -------------------------------
            $stmt = $conn->prepare("
                UPDATE tbl_userbudgetversion
                SET userBudgetRuleID = ?
                WHERE userID = ? AND isActive = 1
            ");
            $stmt->bind_param("ii", $ruleID, $userID);
            $stmt->execute();
            $stmt->close();

            // Insert allocations
            $stmt = $conn->prepare("
                INSERT INTO tbl_userallocation
                (userBudgetruleID, userCategoryID, necessityType, limitType, value)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($allocations as $a) {
                $stmt->bind_param(
                    "iisid",
                    $ruleID,
                    $a['userCategoryID'],
                    $a['necessityType'],
                    $a['limitType'],
                    $a['value']
                );
                $stmt->execute();
            }
            $stmt->close();

            // Update category modes
            $stmt = $conn->prepare("
                UPDATE tbl_usercategories
                SET userisFlexible = ?
                WHERE userCategoryID = ? AND userID = ?
            ");

            foreach ($categories as $c) {
                $id = $c['userCategoryID'];
                $mode = $_POST["category_{$id}_mode"] ?? 'track';
                $flex = ($mode === "limit") ? 1 : 0;

                $stmt->bind_param("iii", $flex, $id, $userID);
                $stmt->execute();
            }
            $stmt->close();

            $conn->commit();
            header("Location: done.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed: " . $e->getMessage();
        }
    }
}
?>
