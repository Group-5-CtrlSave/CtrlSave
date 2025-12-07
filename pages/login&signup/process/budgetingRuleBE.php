<?php
session_start();
include("../../assets/shared/connect.php");

$error = "";

if (!isset($_SESSION['userID'])) {
    header("Location: ../login&signup/login.php");
    exit;
}

$userID = (int) $_SESSION['userID'];

// Currency setup (display only)
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';


// ----------------------------------------------------------
// FETCH DEFAULT RULES + ALLOCATIONS
// ----------------------------------------------------------
$sql = "
    SELECT r.defaultBudgetruleID, r.ruleName, r.ruleDescription,
           a.defaultnecessityType AS category, a.value AS percentage
    FROM tbl_defaultbudgetrule r
    LEFT JOIN tbl_defaultallocation a
        ON r.defaultBudgetruleID = a.defaultBudgetruleID
    ORDER BY r.defaultBudgetruleID, a.defaultAllocationID
";
$res = $conn->query($sql);

$rules = [];
while ($row = $res->fetch_assoc()) {
    $id = $row['defaultBudgetruleID'];

    if (!isset($rules[$id])) {
        $rules[$id] = [
            'id' => $id,
            'ruleName' => $row['ruleName'],
            'ruleDescription' => $row['ruleDescription'],
            'allocations' => []
        ];
    }

    if ($row['category'] !== null) {
        $rules[$id]['allocations'][] = [
            'category' => $row['category'],
            'percentage' => (int) $row['percentage']
        ];
    }
}



// ----------------------------------------------------------
// HANDLE POST
// ----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // USER PICKS CUSTOM RULE
    if (isset($_POST['preferMine'])) {
        header("Location: percentage.php");
        exit;
    }

    // USER PICKS DEFAULT RULE
    if (!isset($_POST['useDefault'])) {
        $error = "Invalid submission. Please select a budgeting rule.";
    } else {

        if (empty($_POST['ruleOption'])) {
            $error = "Please select a budget rule.";
        } else {

            $selectedRule = (int) $_POST['ruleOption'];

            if (!isset($rules[$selectedRule])) {
                $error = "Invalid rule selected.";
            } else {

                $selectedRuleName = $rules[$selectedRule]['ruleName'];

                // -------------------------------------------------
                // BEGIN TRANSACTION
                // -------------------------------------------------
                $conn->begin_transaction();

                try {

                    // -------------------------------------------------
                    // STEP 1 — UNSELECT ALL CURRENT RULES FOR USER
                    // -------------------------------------------------
                    $un = $conn->prepare("
                        UPDATE tbl_userbudgetrule
                        SET isSelected = 0
                        WHERE userID = ?
                    ");
                    $un->bind_param("i", $userID);
                    $un->execute();
                    $un->close();


                    // -------------------------------------------------
                    // STEP 2 — CHECK IF USER HAS ANY RULE (UPDATE/INSERT)
                    // -------------------------------------------------
                    $check = $conn->prepare("
                        SELECT userBudgetRuleID
                        FROM tbl_userbudgetrule
                        WHERE userID = ?
                        LIMIT 1
                    ");
                    $check->bind_param("i", $userID);
                    $check->execute();
                    $result = $check->get_result();
                    $check->close();

                    if ($result->num_rows > 0) {

                        // USER ALREADY HAS A RULE → UPDATE IT
                        $row = $result->fetch_assoc();
                        $userBudgetRuleID = (int) $row['userBudgetRuleID'];

                        $upd = $conn->prepare("
                            UPDATE tbl_userbudgetrule
                            SET ruleName = ?, isSelected = 1
                            WHERE userBudgetRuleID = ?
                        ");
                        $upd->bind_param("si", $selectedRuleName, $userBudgetRuleID);
                        $upd->execute();
                        $upd->close();

                    } else {

                        // USER HAS NO RULE → INSERT NEW RULE
                        $ins = $conn->prepare("
                            INSERT INTO tbl_userbudgetrule (userID, ruleName, createdAt, isSelected)
                            VALUES (?, ?, NOW(), 1)
                        ");
                        $ins->bind_param("is", $userID, $selectedRuleName);
                        $ins->execute();
                        $userBudgetRuleID = $ins->insert_id;
                        $ins->close();
                    }


                    // -------------------------------------------------
                    // STEP 3 — ATTACH RULE TO ACTIVE VERSION
                    // -------------------------------------------------
                    $stmt = $conn->prepare("
                        UPDATE tbl_userbudgetversion
                        SET userBudgetRuleID = ?
                        WHERE userID = ? AND isActive = 1
                    ");
                    $stmt->bind_param("ii", $userBudgetRuleID, $userID);
                    $stmt->execute();
                    $stmt->close();


                    // -------------------------------------------------
                    // STEP 4 — REPLACE ALL USER ALLOCATIONS (DEFAULT RULE)
                    // -------------------------------------------------
                    $del = $conn->prepare("
                        DELETE FROM tbl_userallocation
                        WHERE userBudgetruleID = ?
                    ");
                    $del->bind_param("i", $userBudgetRuleID);
                    $del->execute();
                    $del->close();


                    // INSERT PERCENTAGE ALLOCATIONS
// For default rules, userCategoryID should always be 0
                    $insertAlloc = $conn->prepare("
    INSERT INTO tbl_userallocation
        (userBudgetruleID, userCategoryID, necessityType, limitType, value)
    VALUES (?, 0, ?, 1, ?)
");

                    foreach ($rules[$selectedRule]['allocations'] as $alloc) {
                        $necessity = $alloc['category'];
                        $percent = $alloc['percentage'];

                        $insertAlloc->bind_param("isi", $userBudgetRuleID, $necessity, $percent);
                        $insertAlloc->execute();
                    }

                    $insertAlloc->close();


                    // -------------------------------------------------
                    // COMMIT TRANSACTION
                    // -------------------------------------------------
                    $conn->commit();
                    header("Location: done.php");
                    exit;

                } catch (Exception $e) {

                    $conn->rollback();
                    $error = "Failed to save selected rule.";
                }
            }
        }
    }
}
?>