<?php
$getExpensesCategoriesQuery = "SELECT userCategoryID, categoryName, icon FROM tbl_usercategories WHERE userID= 1 AND type = 'expense' AND isSelected = 1";
$expenseCategoriesResult = executeQuery($getExpensesCategoriesQuery);
?>

<?php

if (isset($_POST['addExpense'])) {
    $amount = $_POST['amount'];
    $note = $_POST['note'];
    $dueDate = $_POST['dueDate'];
    $isRecurring = $_POST['recurringPayment'] ?? '';
    $categoryID = $_POST['categoryID'];
    $frequency = $_POST['frequency'] ?? '';

    $nextDueDate = '';
    if (empty($dueDate)) {
    switch ($frequency) {
        case 'daily':
            $nextDueDate = 'NOW() + INTERVAL 1 DAY';
        break;
        case 'weekly':
            $nextDueDate = 'NOW() + INTERVAL 1 WEEK';
        break;
        case 'monthly': 
            $nextDueDate = 'NOW() + INTERVAL 1 MONTH';
        break;
        default :
            $nextDueDate = '';
        break;
    }
} else if (!empty($dueDate)) {
    switch ($frequency) {
        case 'daily':
            $nextDueDate = "'" . $dueDate . "' + INTERVAL 1 DAY";
            break;

        case 'weekly':
            $nextDueDate = "'" . $dueDate . "' + INTERVAL 1 WEEK";
            break;

        case 'monthly':
            $nextDueDate = "'" . $dueDate . "' + INTERVAL 1 MONTH";
            break;

        default:
            $nextDueDate = '';
            break;
    }
}
     ($isRecurring) ? $addRecurringTransactionQuery = "INSERT INTO `tbl_recurringtransactions`(`userID`, `type`, `userCategoryID`, `amount`, `note`, `frequency`, `nextDuedate`, `isActive`) 
    VALUES ('1','expenses','$categoryID','$amount','$note','$frequency',$nextDueDate,'1')": '';
    ($isRecurring) ?  executeQuery($addRecurringTransactionQuery) : '';

    $lastRecurringID = mysqli_insert_id($conn) ?? '';


    (!empty($dueDate)) ? $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`, `recurringID`,`userBudgetversionID`) 
        VALUES ('1','$amount','$categoryID','','$dueDate','$isRecurring','$note', '$lastRecurringID', '1')" :  $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dueDate`, `isRecurring`, `note`,  `recurringID` , `userBudgetversionID`) 
        VALUES ('1','$amount','$categoryID',NULL,'$isRecurring','$note', '$lastRecurringID' , '1')" ;
    
   
    executeQuery($addExpensesQuery);

   


}



?>