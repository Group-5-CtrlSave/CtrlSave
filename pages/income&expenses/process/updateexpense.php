<?php
if (isset($_POST['btnPaid'])) {
    $expenseID = $_POST['expenseID'];
    $updateDateSpentQuery = "UPDATE `tbl_expense` 
    SET `dateSpent`=DEFAULT ,`dueDate`= NULL WHERE expenseID = $expenseID";
    executeQuery($updateDateSpentQuery);

    $_SESSION["successtag"] = "Expense Updated Successfully!";

    header("Location: " . $_SERVER['PHP_SELF'] . "?type=expense" . "&id=" . $expenseID);
    exit;
}
?>

<?php
$getExpenseCategoriesQuery = "SELECT userCategoryID, categoryName, icon
FROM `tbl_usercategories` WHERE userID = 1 AND type = 'expense' AND isSelected = 1";
$expenseCategoriesResult = executeQuery($getExpenseCategoriesQuery);
?>

<?php
if (isset($_POST['saveButton'])) {
    $updatedUserCategoryID = $_POST['userCategory'];
    $updatedAmount = $_POST['amount'];
    $updatedDate = $_POST['date'];
    $updatedNote = $_POST['note'];
    $updatedisRecurring = $_POST['isRecurring'] ?? '0';
    $updatedFrequency = $_POST['frequency'] ?? '';

   $checkExistingTransactionQuery = "SELECT COUNT(*) AS total 
    FROM tbl_recurringtransactions 
    WHERE userID = 1 AND recurringID = $recurringID";
    $checkExistingTransactionResult = executeQuery($checkExistingTransactionQuery);
    $existingTransaction = mysqli_fetch_assoc( $checkExistingTransactionResult);
    $exists = $existingTransaction['total'] > 0;
    



    $nextDueDate = '';

    switch ($updatedFrequency) {
        case 'daily':
            $nextDueDate = "'" . $updatedDate . "' + INTERVAL 1 DAY";
            break;

        case 'weekly':
            $nextDueDate = "'" . $updatedDate . "' + INTERVAL 1 WEEK";
            break;

        case 'monthly':
            $nextDueDate = "'" . $updatedDate . "' + INTERVAL 1 MONTH";
            break;

        default:
            $nextDueDate = '';
            break;

    }
    $lastRecurringID = '';

    if ($updatedisRecurring != 0) {

        if ($exists) {

            $updateRecurringTransactionQuery = "UPDATE `tbl_recurringtransactions` 
            SET `userCategoryID`='$updatedUserCategoryID',`amount`='$updatedAmount',`note`='$updatedNote',`frequency`='$updatedFrequency'
            WHERE userID = 1 and recurringID = $recurringID";
            executeQuery($updateRecurringTransactionQuery);

        } else {
            $addRecurringTransactionQuery = "INSERT INTO `tbl_recurringtransactions`(`userID`, `type`, `userCategoryID`, `amount`, `note`, `frequency`, `nextDuedate`) 
    VALUES ('1','expenses','$updatedUserCategoryID','$updatedAmount','$updatedNote','$updatedFrequency', $nextDueDate)";
            executeQuery($addRecurringTransactionQuery);
            $lastRecurringID = mysqli_insert_id($conn) ?? '';

        }
    } else {
        if (!empty($recurringID)) {
        $deleteRecurringTransactionQuery = "DELETE FROM tbl_recurringtransactions WHERE recurringID = $recurringID AND userID = 1";
        executeQuery($deleteRecurringTransactionQuery);
    }
    }

    (!empty($dueDate)) ? $udpateExpenseQuery = "UPDATE `tbl_expense` 
SET `amount`='$updatedAmount',`userCategoryID`='$updatedUserCategoryID',`dateSpent`='',`dueDate`='$updatedDate',`isRecurring`='$updatedisRecurring',`note`='$updatedNote', `recurringID` ='$lastRecurringID'
WHERE userID = 1 and expenseID = $id" : $udpateExpenseQuery = "UPDATE `tbl_expense` 
SET `amount`='$updatedAmount',`userCategoryID`='$updatedUserCategoryID',`dateSpent`='$updatedDate',`dueDate`=NULL,`isRecurring`='$updatedisRecurring',`note`='$updatedNote', `recurringID` ='$lastRecurringID'
WHERE userID = 1 and expenseID = $id";

    executeQuery($udpateExpenseQuery);

}




?>