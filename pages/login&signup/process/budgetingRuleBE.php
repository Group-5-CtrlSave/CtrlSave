<?php
session_start();
include("../../assets/shared/connect.php");

$error = "";

if (!isset($_SESSION['userID'])) {

    header("Location: ../login&signup/login.php");
    exit;
}
$userID = (int) $_SESSION['userID'];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // If user prefers own percentages
    if (isset($_POST['preferMine'])) {
        header("Location: percentage.php");
        exit;
    }

    // Validate selected rule
    if (empty($_POST['ruleOption'])) {
        $error = "Please select a budget rule before proceeding.";
    } else {
        $selectedRule = (int) $_POST['ruleOption'];

        // Confirm selected rule exists in fetched rules
        if (!isset($rules[$selectedRule])) {
            $error = "Invalid rule selected.";
        } else {
            // Begin transaction
            $conn->begin_transaction();

            try {
                $ruleName = $rules[$selectedRule]['ruleName'];

                // Check if user already has a budget rule
                $check = $conn->prepare("SELECT userBudgetRuleID FROM tbl_userbudgetrule WHERE userID = ?");
                $check->bind_param("i", $userID);
                $check->execute();
                $chkRes = $check->get_result();

                if ($chkRes->num_rows > 0) {
            
                    $existingRow = $chkRes->fetch_assoc();
                    $userBudgetRuleID = (int) $existingRow['userBudgetRuleID'];

                    // Update existing row to reflect chosen ruleName and isSelected=1
                    $upd = $conn->prepare("UPDATE tbl_userbudgetrule SET ruleName = ?, isSelected = 1 WHERE userBudgetRuleID = ?");
                    $upd->bind_param("si", $ruleName, $userBudgetRuleID);
                    $upd->execute();
                } else {
                    // Insert a new userBudgetRule row
                    $ins = $conn->prepare("INSERT INTO tbl_userbudgetrule (userID, ruleName, createdAt, isSelected) VALUES (?, ?, NOW(), 1)");
                    $ins->bind_param("is", $userID, $ruleName);
                    $ins->execute();
                    $userBudgetRuleID = $conn->insert_id;
                }

                // Delete any existing allocations for this userBudgetRuleID (to avoid duplicates)
                $del = $conn->prepare("DELETE FROM tbl_userallocation WHERE userBudgetruleID = ?");
                $del->bind_param("i", $userBudgetRuleID);
                $del->execute();

                // Insert allocations from default into tbl_userallocation
                $insertAlloc = $conn->prepare("INSERT INTO tbl_userallocation (userBudgetruleID, userCategoryID, necessityType, limitType, value) VALUES (?, NULL, ?, 0, ?)");
                foreach ($rules[$selectedRule]['allocations'] as $alloc) {
                    $necessity = $alloc['category'];  
                    $value = (int) $alloc['percentage'];
                    $insertAlloc->bind_param("isi", $userBudgetRuleID, $necessity, $value); 
                    $insertAlloc->execute();
                }

                $conn->commit();
                header("Location: done.php");
                exit;
            } catch (Exception $ex) {
                $conn->rollback();
                $error = "Failed to save selected rule. Please try again.";
            }
        }
    }
}
?>