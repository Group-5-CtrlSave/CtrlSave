<?php
// userID
$userID = '';
if (isset($_SESSION['userID'])){
    $userID = $_SESSION['userID'];
   
}
?>

<?php
$getIncomeCategoriesQuery = "SELECT userCategoryID, categoryName, icon  FROM tbl_usercategories WHERE userID = $userID AND type = 'income' AND isSelected = 1";
$incomeCategoriesResult = executeQuery($getIncomeCategoriesQuery);
?>


<?php

include('../challenge/process/challengeController.php');

if (isset($_POST['addIncome']) && $_POST['amount'] != 0){
    $amount = $_POST['amount'];
    $note = $_POST['note'];
    $categoryID = $_POST['categoryID'];
    $date = !empty($_POST['date']) ? $_POST['date'] : NULL;

    
   

  
        $versionNumber = 0;
        $userBudgetRuleID = 0;
        $totalIncome = 0.00;
        $userBudgetversionID = 0;

    $selectUserBudgetVersionQuery = "SELECT 'userBudgerVersionID', `versionNumber`, `userBudgetRuleID`, `totalIncome` 
    FROM `tbl_userbudgetversion` WHERE userID = $userID AND isActive = 1";
    $userBudgetVersionResult = executeQuery($selectUserBudgetVersionQuery);
    if (mysqli_num_rows($userBudgetVersionResult) > 0){
        $row = mysqli_fetch_assoc($userBudgetVersionResult);
        $userBudgetversionID = ['userBudgetVersionID'];
        $versionNumber = $row['versionNumber'];
        $userBudgetRuleID = $row['userBudgetRuleID'];
        $totalIncome = (float)$row['totalIncome'];
    }

    $createNewBudgetVersionQuery = "INSERT INTO `tbl_userbudgetversion`(`versionNumber`, `userID`, `userBudgetRuleID`, `totalIncome`, `isActive`) 
    VALUES ($versionNumber + 1,'$userID','$userBudgetRuleID',$totalIncome + $amount, '1')";
    executeQuery($createNewBudgetVersionQuery);

    $_SESSION['userBudgetVersionID'] = mysqli_insert_id($conn) ?? $userBudgetversionID; 
    $lastbudgetVersionID = $_SESSION['userBudgetVersionID'];

    $updateLastuserbudgetversion = "UPDATE `tbl_userbudgetversion` SET `isActive`='0' 
    WHERE userID = $userID and versionNumber = $versionNumber";
    executeQuery($updateLastuserbudgetversion);

      (!empty($date)) ? $addIncomeQuery = "INSERT INTO tbl_income( `userID`, `amount`, `dateReceived`, `note`, `userCategoryID`, `userBudgetversionID`) 
    VALUES ('$userID','$amount',CONCAT('$date', ' ', CURTIME()),'$note','$categoryID', '1')" : $addIncomeQuery = "INSERT INTO tbl_income( `userID`, `amount`, `dateReceived`, `note`, `userCategoryID`, `userBudgetversionID`) 
    VALUES ('$userID','$amount',DEFAULT,'$note','$categoryID', '$lastbudgetVersionID')";

    executeQuery($addIncomeQuery);



    // AUTO UPDATE WEEKLY INCOME CHALLENGE
    updateIncomeChallenges($userID, $conn);
    
    $_SESSION['successtag'] = "Income added Successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>