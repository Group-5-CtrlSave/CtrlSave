<?php include('../../assets/shared/scripts/dailyoverspendingfunction.php') ?>
<?php
// userID
$userID = '';

include('../challenge/process/challengeController.php');


if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

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
    ($isRecurring) ? $addRecurringTransactionQuery = "INSERT INTO `tbl_recurringtransactions`(`userID`, `type`, `userCategoryID`, `amount`, `note`, `frequency`, `nextDuedate`) 
    VALUES ('$userID','expenses','$categoryID','$amount','$note','$frequency',$nextDueDate)" : '';
    ($isRecurring) ? executeQuery($addRecurringTransactionQuery) : '';

    $lastRecurringID = mysqli_insert_id($conn) ?? '';

    if (!empty($date)) {
        $now = date("Y-m-d");
        $target = $date;
        if ($now > $target) {
             $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`,  `recurringID` , `userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',CONCAT('$date', ' ', CURTIME()),NULL,'$isRecurring','$note', '$lastRecurringID' , '1')";
            

        } else if ($now < $target) {
                $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`, `recurringID`,`userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',NULL,'$date','$isRecurring','$note', '$lastRecurringID', '1')";
        
        } else if ($now == $target) {
            $addExpensesQuery = "INSERT INTO tbl_expense ( `userID`, `amount`, `userCategoryID`,`dateSpent`,`dueDate`, `isRecurring`, `note`,  `recurringID` , `userBudgetversionID`) 
        VALUES ('$userID','$amount','$categoryID',DEFAULT,NULL,'$isRecurring','$note', '$lastRecurringID' , '1')";

        }
    }





    executeQuery($addExpensesQuery);
    checkDailyOverspending($userID);

    updateExpenseChallenges($userID, $conn);

    $_SESSION["successtag"] = "Expense succefully added!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;





}



?>