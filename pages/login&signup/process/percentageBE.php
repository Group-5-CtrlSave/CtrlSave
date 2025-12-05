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
$categories = [];
$savingsCategory = null;
$error = '';
$show_tracking_prompt = false;

function clean_currency_input($value)
{
    $cleaned_value = preg_replace('/[,\s\x{20B1}\$]/u', '', $value);
    return trim($cleaned_value);
}

/* ----------------------------------------------------------
   FETCH EXPENSE CATEGORIES
----------------------------------------------------------- */
$sql_categories = "SELECT userCategoryID, categoryName, userisFlexible, userNecessityType 
                   FROM tbl_usercategories 
                   WHERE userID = ? 
                     AND type = 'expense' 
                     AND isSelected = 1 
                   ORDER BY userNecessityType DESC, categoryName ASC";
$stmt_categories = $conn->prepare($sql_categories);
if ($stmt_categories) {
    $stmt_categories->bind_param("i", $userID);
    $stmt_categories->execute();
    $result_categories = $stmt_categories->get_result();
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
    $stmt_categories->close();
} else {
    $error = "Database error fetching expense categories: " . $conn->error;
}

/* ----------------------------------------------------------
   FETCH SAVINGS CATEGORY
----------------------------------------------------------- */
$sql_savings_id = "SELECT userCategoryID, categoryName 
                   FROM tbl_usercategories 
                   WHERE userID = ? 
                     AND type = 'savings' 
                     AND categoryName = 'Savings'";
$stmt_savings = $conn->prepare($sql_savings_id);
if ($stmt_savings) {
    $stmt_savings->bind_param("i", $userID);
    $stmt_savings->execute();
    $result_savings = $stmt_savings->get_result();
    $savingsCategory = $result_savings->fetch_assoc();
    $stmt_savings->close();

    if (!$savingsCategory) {
        $error = "CRITICAL ERROR: 'Savings' category not found. Cannot proceed.";
    }
} else {
    $error = "Database error fetching Savings category: " . $conn->error;
}

/* ----------------------------------------------------------
   FETCH TOTAL INCOME FOR JS + VALIDATION (OUTSIDE POST!)
----------------------------------------------------------- */
$totalIncome = 0.00;
$sql_income = "
    SELECT totalIncome
    FROM tbl_userbudgetversion
    WHERE userID = ?
      AND isActive = 1
    ORDER BY userBudgetversionID DESC
    LIMIT 1
";
$stmt_income = $conn->prepare($sql_income);
if ($stmt_income) {
    $stmt_income->bind_param("i", $userID);
    $stmt_income->execute();
    $res_income = $stmt_income->get_result();
    if ($row = $res_income->fetch_assoc()) {
        $totalIncome = (float)$row['totalIncome'];
    }
    $stmt_income->close();
}

// Make total income available to JavaScript (FOR LIVE BALANCE UI)
echo "<script>window.userIncome = {$totalIncome};</script>";

/* ----------------------------------------------------------
   HANDLE POST REQUEST
----------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {

    $all_tracking   = true;
    $valid_allocation = true;
    $allocations    = [];
    $total_limit_count = 0;

    /* -----------------------------
       PROCESS EXPENSE CATEGORIES
    ------------------------------ */
    if (!empty($categories)) {
        foreach ($categories as $category) {

            $id = $category['userCategoryID'];
            $name_base = "category_" . $id;

            $mode  = $_POST[$name_base . "_mode"] ?? '';
            $value = trim($_POST[$name_base . "_value"] ?? '');
            $clean = clean_currency_input($value);

            if (!$mode) {
                $valid_allocation = false;
                $error = "Internal Error: Missing mode for a category.";
                break;
            }

            // limitType: 1 = percentage, 0 = amount
            $allocation = [
                'userCategoryID' => $id,
                'necessityType' => $category['userNecessityType'],
                'limitType' => ($mode == 'limit') ? 1 : 0,
                'value' => 0.00
            ];

            if ($mode == 'limit') {
                $all_tracking = false;
                $total_limit_count++;

                if (empty($value) || !is_numeric($clean) || (float)$clean <= 0) {
                    $valid_allocation = false;
                    $error = "Limit for " . htmlspecialchars($category['categoryName']) . " must be a positive amount.";
                    break;
                }

                $allocation['value'] = (float)$clean;
            }

            $allocations[] = $allocation;
        }
    }

    /* -----------------------------
        PROCESS SAVINGS
    ------------------------------ */
    $savings_value = trim($_POST['savings_value'] ?? '');
    $clean_savings = clean_currency_input($savings_value);

    if ($valid_allocation) {
        if (empty($savings_value) || !is_numeric($clean_savings) || (float)$clean_savings <= 0) {
            $valid_allocation = false;
            $error = "Savings amount must be positive.";
        } else {
            $allocations[] = [
                'userCategoryID' => $savingsCategory['userCategoryID'],
                'necessityType' => 'saving',
                'limitType' => 1, // ALWAYS percentage-based limit
                'value' => (float)$clean_savings
            ];
            $total_limit_count++;
        }
    }

    /* -----------------------------
        OPTIONAL TRACKING-ONLY PROMPT
    ------------------------------ */
    if ($valid_allocation && $all_tracking && $total_limit_count == 1) {
        if (!isset($_POST['tracking_confirm'])) {
            $show_tracking_prompt = true;
            $valid_allocation = false;
        }
    }

    /* -----------------------------
        VALIDATE AGAINST TOTAL INCOME
    ------------------------------ */
    if ($valid_allocation && !$show_tracking_prompt) {

        if ($totalIncome <= 0) {
            $valid_allocation = false;
            $error = "You must set a valid monthly income before creating limits.";
        } else {
            $sumLimitAmounts = 0.00;

            foreach ($allocations as $a) {
                if ((int)$a['limitType'] === 1) {
                    $sumLimitAmounts += (float)$a['value'];
                }
            }

            if ($sumLimitAmounts > $totalIncome) {
                $percent = ($sumLimitAmounts / $totalIncome) * 100;
                $valid_allocation = false;
                $error = "Your limits exceed 100% of your income.";
            }
        }
    }

    /* -----------------------------
        SAVE FINAL DATA INTO DATABASE
    ------------------------------ */
    if ($valid_allocation && !$show_tracking_prompt) {

        $conn->begin_transaction();

        try {
            // Unselect previous rules
            $sql_un = "UPDATE tbl_userbudgetrule SET isSelected = 0 WHERE userID = ?";
            $stmt_un = $conn->prepare($sql_un);
            $stmt_un->bind_param("i", $userID);
            $stmt_un->execute();
            $stmt_un->close();

            // Insert new rule
            $ruleName = "Custom-Rule";
            $sql_new = "INSERT INTO tbl_userbudgetrule (userID, ruleName, isSelected) VALUES (?, ?, 1)";
            $stmt_new = $conn->prepare($sql_new);
            $stmt_new->bind_param("is", $userID, $ruleName);
            $stmt_new->execute();
            $ruleID = $conn->insert_id;
            $stmt_new->close();

            // Insert allocations
            $sql_alloc = "INSERT INTO tbl_userallocation 
                          (userBudgetruleID, userCategoryID, necessityType, limitType, value)
                          VALUES (?, ?, ?, ?, ?)";
            $stmt_alloc = $conn->prepare($sql_alloc);

            foreach ($allocations as $a) {
                $stmt_alloc->bind_param(
                    "iisid",
                    $ruleID,
                    $a['userCategoryID'],
                    $a['necessityType'],
                    $a['limitType'],
                    $a['value']
                );
                $stmt_alloc->execute();
            }

            $stmt_alloc->close();

            // Update category modes
            $sql_upd = "UPDATE tbl_usercategories SET userisFlexible = ? WHERE userCategoryID = ? AND userID = ?";
            $stmt_upd = $conn->prepare($sql_upd);

            foreach ($categories as $category) {
                $id = $category['userCategoryID'];
                $mode = $_POST["category_{$id}_mode"] ?? 'track';
                $flex = ($mode === 'limit') ? 1 : 0;

                $stmt_upd->bind_param("iii", $flex, $id, $userID);
                $stmt_upd->execute();
            }

            $stmt_upd->close();

            $conn->commit();
            header("Location: done.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Transaction failed: " . $e->getMessage();
        }
    }
}
?>