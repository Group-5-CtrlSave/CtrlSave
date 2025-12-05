<?php include('../../assets/shared/scripts/dailyoverspendingfunction.php') ?>


<?php

include('../challenge/process/challengeController.php');

// userID
$userID = 0;
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    

}


?>

<?php
$userBudgetVersionID = 0;
$selectUserBudgetVersionQuery = "SELECT `userBudgetVersionID`, `versionNumber`, `userBudgetRuleID`, `totalIncome` 
    FROM `tbl_userbudgetversion` WHERE userID = $userID AND isActive = 1";
    $userBudgetVersionResult = executeQuery($selectUserBudgetVersionQuery);
    if (mysqli_num_rows($userBudgetVersionResult) > 0){
        $row = mysqli_fetch_assoc($userBudgetVersionResult);
        $userBudgetVersionID = $row['userBudgetVersionID'] ?? 0;
    }

?>

<?php
$getExpensesCategoriesQuery = "SELECT userCategoryID, categoryName, icon FROM tbl_usercategories WHERE userID= $userID AND type = 'expense' AND isSelected = 1";
$expenseCategoriesResult = executeQuery($getExpensesCategoriesQuery);
?>

<?php




if (isset($_POST['addExpense']) && $_POST['amount'] != 0) {
    $amount = $_POST['amount'];
    $note = $_POST['note'];
    $date = $_POST['date'];
    $isRecurring = $_POST['recurringPayment'] ?? '';
    $categoryID = $_POST['categoryID'];
    $frequency = $_POST['frequency'] ?? '';



    $nextDueDate = '';
    if (empty($date)) {
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
            default:
                $nextDueDate = '';
                break;
        }
    } else if (!empty($date)) {
        switch ($frequency) {
            case 'daily':
                $nextDueDate = "'" . $date . "' + INTERVAL 1 DAY";
                break;

            case 'weekly':
                $nextDueDate = "'" . $date . "' + INTERVAL 1 WEEK";
                break;

            case 'monthly':
                $nextDueDate = "'" . $date . "' + INTERVAL 1 MONTH";
                break;

            default:
                $nextDueDate = '';
                break;
        }
    }
    ($isRecurring) ? $addRecurringTransactionQuery = "INSERT INTO `tbl_recurringtransactions`(`userID`, `type`, `userCategoryID`, `amount`, `note`, `frequency`, `nextDuedate`, `userBudgetVersionID`) 
    VALUES ('$userID','expenses','$categoryID','$amount','$note','$frequency',$nextDueDate, $userBudgetVersionID)" : '';
    ($isRecurring) ? executeQuery($addRecurringTransactionQuery) : '';

    $lastRecurringID = mysqli_insert_id($conn) ?? '';

    if (!empty($date)) {
        $now = date("Y-m-d");
        $target = $date;
        if ($now > $target) {
             $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`,  `recurringID` , `userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',CONCAT('$date', ' ', CURTIME()),NULL,'$isRecurring','$note', '$lastRecurringID' , '$userBudgetVersionID ')";
            
        } else if ($now < $target) {
                $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`, `recurringID`,`userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',NULL,'$date','$isRecurring','$note', '$lastRecurringID', '$userBudgetVersionID ')";
        
        } else if ($now == $target) {
            $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`,  `recurringID` , `userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',DEFAULT,NULL,'$isRecurring','$note', '$lastRecurringID' , '$userBudgetVersionID ')";

        }
    }else {
         $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`,  `recurringID` , `userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',DEFAULT,NULL,'$isRecurring','$note', '$lastRecurringID' , '$userBudgetVersionID ')";
    }





    executeQuery($addExpensesQuery);
    checkDailyOverspending($userID);

    updateExpenseChallenges($userID, $conn);

    $_SESSION["successtag"] = "Expense succefully added!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;





}



?>