<?php
// Get Frequency 
$expenseFrequency = '';
if ($recurringID != 0) {
    $getExpenseFrequency = "SELECT r.frequency as frequency 
    FROM tbl_expense e 
    LEFT JOIN tbl_recurringtransactions r 
    ON e.recurringID = r.recurringID
    WHERE e.expenseID = $id AND e.userID = $userID";
    $expenseFrequencyResult = executeQuery($getExpenseFrequency);
    if (mysqli_num_rows($expenseFrequencyResult) > 0) {
        $row = mysqli_fetch_assoc($expenseFrequencyResult);
        $expenseFrequency = $row['frequency'] ?? '';
    }

}

?>
<!-- Get The User Budget Version -->
<?php
$userBudgetVersionID = 0;
$selectUserBudgetVersionQuery = "SELECT `userBudgetVersionID`, `versionNumber`, `userBudgetRuleID`, `totalIncome` 
    FROM `tbl_userbudgetversion` WHERE userID = $userID AND isActive = 1";
$userBudgetVersionResult = executeQuery($selectUserBudgetVersionQuery);
if (mysqli_num_rows($userBudgetVersionResult) > 0) {
    $row = mysqli_fetch_assoc($userBudgetVersionResult);
    $userBudgetVersionID = $row['userBudgetVersionID'] ?? 0;
}

?>

<!-- Update the Expense if Paid -->
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
<!-- DELETE EXPENSE -->
<?php
if (isset($_POST["btnDelete"])) {
    switch ($type) {
        case "income":
            $deleteIncomeQuery = "DELETE FROM `tbl_income` WHERE incomeID = $id and userID = $userID";
            executeQuery($deleteIncomeQuery);

             $totalUserIncome = 0;
            $getTotalIncome = "SELECT SUM(`amount`) as income FROM `tbl_income` WHERE userID = $userID";
            $totalIncomeResult = executeQuery($getTotalIncome);
            if (mysqli_num_rows($totalIncomeResult)) {
                $row = mysqli_fetch_assoc($totalIncomeResult);
                $totalUserIncome = $row['income'];

            }
            $updateBudgetVersionQuery = "UPDATE `tbl_userbudgetversion` SET `totalIncome`='$totalUserIncome' WHERE userID = $userID and isActive = 1";
            executeQuery($updateBudgetVersionQuery);


            $_SESSION["successtag"] = "Income Successfully Deleted!";
            header("Location: income_expenses.php");
            break;

        case "expense":
            $deleteRecurringTransactionQuery = "DELETE FROM tbl_recurringtransactions WHERE recurringID = $recurringID AND userID = $userID";
            executeQuery($deleteRecurringTransactionQuery);
            $deleteExpenseQuery = "DELETE FROM `tbl_expense` WHERE expenseID = $id and userID = $userID";
            $_SESSION['successtag'] = "Expense Succesfully Deleted";
            header("Location: income_expenses.php");
            executeQuery($deleteExpenseQuery);
            break;
    }
}

?>




<?php
if (isset($_POST['saveButton'])) {
    switch ($type) {
        // UPDATE INCOME
        case 'income':
            $updatedUserCategoryID = $_POST['userCategory'];
            $updatedAmount = $_POST['amount'];
            $updatedDate = $_POST['date'];
            $updatedNote = $_POST['note'];


            $updateIncomeQuery = "UPDATE `tbl_income` 
            SET `amount`='$updatedAmount',`dateReceived`=CONCAT('$updatedDate', ' ', CURTIME()),`note`='$updatedNote',`userCategoryID`='$updatedUserCategoryID',`userBudgetversionID`='1' 
            WHERE userID = $userID AND incomeID = $id ";

            executeQuery($updateIncomeQuery);

            $totalUserIncome = 0;
            $getTotalIncome = "SELECT SUM(`amount`) as income FROM `tbl_income` WHERE userID = $userID";
            $totalIncomeResult = executeQuery($getTotalIncome);
            if (mysqli_num_rows($totalIncomeResult)) {
                $row = mysqli_fetch_assoc($totalIncomeResult);
                $totalUserIncome = $row['income'];

            }
            $updateBudgetVersionQuery = "UPDATE `tbl_userbudgetversion` SET `totalIncome`='$totalUserIncome' WHERE userID = $userID and isActive = 1";
            executeQuery($updateBudgetVersionQuery);

            header("Location: viewIncomeExpense.php?type=income&id=$id");




            break;
        // UPDATE EXPENSE
        case 'expense':
            $updatedUserCategoryID = $_POST['userCategory'];
            $updatedAmount = $_POST['amount'];
            $updatedDate = $_POST['date'];
            $updatedNote = $_POST['note'];
            $updatedisRecurring = $_POST['isRecurring'] ?? '0';
            $updatedFrequency = $_POST['frequency'] ?? '';

            $checkExistingTransactionQuery = "SELECT COUNT(*) AS total 
            FROM tbl_recurringtransactions 
            WHERE userID = $userID AND recurringID = $recurringID";

            $checkExistingTransactionResult = executeQuery($checkExistingTransactionQuery);
            $existingTransaction = mysqli_fetch_assoc($checkExistingTransactionResult);
            $exists = $existingTransaction['total'] > 0;
            $lastRecurringID = 0;


            if ($updatedisRecurring != 0) {
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



                if ($exists) {
                    // IF TRANSACTION EXIST UPDATE IT
                    $updateRecurringTransactionQuery = "UPDATE `tbl_recurringtransactions` 
                    SET `userCategoryID`='$updatedUserCategoryID',`amount`='$updatedAmount',`note`='$updatedNote',`frequency`='$updatedFrequency', `nextDuedate` = $nextDueDate
                    WHERE userID = $userID and recurringID = $recurringID";
                    executeQuery($updateRecurringTransactionQuery);

                } else {
                    // IF TRANSACTION DON'T EXIST INSERT
                    $addRecurringTransactionQuery = "INSERT INTO `tbl_recurringtransactions`(`userID`, `type`, `userCategoryID`, `amount`, `note`, `frequency`, `nextDuedate`, `userBudgetVersionID`) 
                    VALUES ('$userID','expenses','$updatedUserCategoryID','$updatedAmount','$updatedNote','$updatedFrequency', $nextDueDate, $userBudgetVersionID)";
                    executeQuery($addRecurringTransactionQuery);
                    $lastRecurringID = mysqli_insert_id($conn);



                }
            } else {
                if (!empty($recurringID)) {
                    $deleteRecurringTransactionQuery = "DELETE FROM tbl_recurringtransactions WHERE recurringID = $recurringID AND userID = $userID";
                    executeQuery($deleteRecurringTransactionQuery);

                    $udpateExpenseQuery = "UPDATE `tbl_expense` 
                    SET  `recurringID` = 0
                    WHERE userID = $userID and expenseID = $id";
                    executeQuery($udpateExpenseQuery);
                    $recurringID = 0;


                }
            }

            $finalRecurringID = ($lastRecurringID != 0) ? $lastRecurringID : $recurringID;


            if (!empty($updatedDate)) {
                $now = date("Y-m-d");
                $target = $updatedDate;
                // PAST
                if ($now > $target) {
                    $udpateExpenseQuery = "UPDATE `tbl_expense` 
            SET `amount`='$updatedAmount',`userCategoryID`='$updatedUserCategoryID',`dateSpent`=CONCAT('$updatedDate', ' ', CURTIME()),`dueDate`=NULL,`isRecurring`='$updatedisRecurring',`note`='$updatedNote', `recurringID` ='$finalRecurringID'
            WHERE userID = $userID and expenseID = $id";
                    // FUTURE
                } else if ($now < $target) {
                    $udpateExpenseQuery = "UPDATE `tbl_expense` 
            SET `amount`='$updatedAmount',`userCategoryID`='$updatedUserCategoryID',`dateSpent`=NULL,`dueDate`='$updatedDate',`isRecurring`='$updatedisRecurring',`note`='$updatedNote', `recurringID` = '$finalRecurringID'
            WHERE userID = $userID and expenseID = $id";
                    // PRESENT
                } else if ($now == $target) {
                    $udpateExpenseQuery = "UPDATE `tbl_expense` 
            SET `amount`='$updatedAmount',`userCategoryID`='$updatedUserCategoryID',`dateSpent`=CONCAT('$updatedDate', ' ', CURTIME()),`dueDate`=NULL,`isRecurring`='$updatedisRecurring',`note`='$updatedNote', `recurringID` ='$finalRecurringID'
            WHERE userID = $userID and expenseID = $id";

                }
            } else {
                $udpateExpenseQuery = "UPDATE `tbl_expense` 
            SET `amount`='$updatedAmount',`userCategoryID`='$updatedUserCategoryID',`dateSpent`=DEFAULT,`dueDate`=NULL,`isRecurring`='$updatedisRecurring',`note`='$updatedNote', `recurringID` ='$finalRecurringID'
            WHERE userID = $userID and expenseID = $id";
            }


            executeQuery($udpateExpenseQuery);

            header("Location: viewIncomeExpense.php?type=expense&id=$id");
            break;


    }


}




?>