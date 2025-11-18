<?php
$getIncomeCategoriesQuery = "SELECT userCategoryID, categoryName, icon  FROM tbl_usercategories WHERE userID = 1 AND type = 'income' AND isSelected = 1";
$incomeCategoriesResult = executeQuery($getIncomeCategoriesQuery);
?>


<?php

if (isset($_POST['addIncome'])){
    $amount = $_POST['amount'];
    $note = $_POST['note'];
    $categoryID = $_POST['categoryID'];
    $date = !empty($_POST['date']) ? $_POST['date'] : NULL;
   

    (!empty($date)) ? $addIncomeQuery = "INSERT INTO tbl_income( `userID`, `amount`, `dateReceived`, `note`, `userCategoryID`, `userBudgetversionID`) 
    VALUES ('1','$amount','$date','$note','$categoryID', '1')" : $addIncomeQuery = "INSERT INTO tbl_income( `userID`, `amount`, `dateReceived`, `note`, `userCategoryID`, `userBudgetversionID`) 
    VALUES ('1','$amount',DEFAULT,'$note','$categoryID', '1')";

    
    executeQuery($addIncomeQuery);
}
?>