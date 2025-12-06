<?php
session_start();
include("../../assets/shared/connect.php");

$error = "";

if (!isset($_SESSION['userID'])) {
    header("Location: ../login&signup/login.php");
    exit;
}

$userID = (int) $_SESSION['userID'];

// Currency setup (just for display)
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';


// ----------------------------------------------------------
// FETCH DEFAULT RULES + THEIR ALLOCATIONS
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
            'percentage' => (int)$row['percentage']
        ];
    }
}



// ----------------------------------------------------------
// HANDLE POST
// ----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // (1) User picked CUSTOM rule
    if (isset($_POST['preferMine'])) {
        header("Location: percentage.php");
        exit;
    }


    // (2) User clicked NEXT (useDefault)
    if (!isset($_POST['useDefault'])) {
        $error = "Invalid submission. Please choose a budget rule properly.";
    } 
    else {

        if (empty($_POST['ruleOption'])) {
            $error = "Please select a budget rule before proceeding.";
        } 
        else {
            $selectedRule = (int)$_POST['ruleOption'];

            if (!isset($rules[$selectedRule])) {
                $error = "Invalid rule selected.";
            } 
            else {

                $selectedRuleName = $rules[$selectedRule]['ruleName'];

                // START TRANSACTION
                $conn->begin_transaction();

                try {

                    // -------------------------------------------------
                    // STEP 1 — Mark ALL user's rules unselected
                    // -------------------------------------------------
                    $unselect = $conn->prepare("
                        UPDATE tbl_userbudgetrule
                        SET isSelected = 0
                        WHERE userID = ?
                    ");
                    $unselect->bind_param("i", $userID);
                    $unselect->execute();
                    $unselect->close();



                    // -------------------------------------------------
                    // STEP 2 — Determine whether to insert or update rule
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
                        // User ALREADY has a rule — update it
                        $row = $result->fetch_assoc();
                        $userBudgetRuleID = (int)$row['userBudgetRuleID'];

                        $update = $conn->prepare("
                            UPDATE tbl_userbudgetrule
                            SET ruleName = ?, isSelected = 1
                            WHERE userBudgetRuleID = ?
                        ");
                        $update->bind_param("si", $selectedRuleName, $userBudgetRuleID);
                        $update->execute();
                        $update->close();

                    } else {

                        // User has NO rule yet → INSERT ONE
                        $insert = $conn->prepare("
                            INSERT INTO tbl_userbudgetrule (userID, ruleName, createdAt, isSelected)
                            VALUES (?, ?, NOW(), 1)
                        ");
                        $insert->bind_param("is", $userID, $selectedRuleName);
                        $insert->execute();
                        $userBudgetRuleID = $insert->insert_id;
                        $insert->close();
                    }


                    // -------------------------------------------------
                    // STEP 3 — Update existing version's rule IF NEEDED
                    // userBudgetVersion was created in balanceBE.php
                    // It had userBudgetRuleID = 0 as placeholder
                    // -------------------------------------------------
                    $updateVersion = $conn->prepare("
                        UPDATE tbl_userbudgetversion
                        SET userBudgetRuleID = ?
                        WHERE userID = ? AND isActive = 1
                    ");
                    $updateVersion->bind_param("ii", $userBudgetRuleID, $userID);
                    $updateVersion->execute();
                    $updateVersion->close();



                    // -------------------------------------------------
                    // STEP 4 — Replace ALL existing user allocations
                    // -------------------------------------------------
                    $delAlloc = $conn->prepare("
                        DELETE FROM tbl_userallocation
                        WHERE userBudgetruleID = ?
                    ");
                    $delAlloc->bind_param("i", $userBudgetRuleID);
                    $delAlloc->execute();
                    $delAlloc->close();


                    // Insert fresh allocations
                    $allocStmt = $conn->prepare("
                        INSERT INTO tbl_userallocation
                                (userBudgetruleID, userCategoryID, necessityType, limitType, value)
                        VALUES  (?, NULL, ?, 1, ?)
                    ");

                    foreach ($rules[$selectedRule]['allocations'] as $alloc) {
                        $necessity = $alloc['category'];
                        $value = (int)$alloc['percentage'];
                        $allocStmt->bind_param("isi", $userBudgetRuleID, $necessity, $value);
                        $allocStmt->execute();
                    }

                    $allocStmt->close();


                    // -------------------------------------------------
                    // COMMIT ALL CHANGES
                    // -------------------------------------------------
                    $conn->commit();

                    header("Location: done.php");
                    exit;


                } catch (Exception $e) {

                    $conn->rollback();
                    $error = "Failed to save your chosen budget rule.";
                }
            }
        }
    }
}
?>
