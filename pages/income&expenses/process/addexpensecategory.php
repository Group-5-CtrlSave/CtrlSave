<?php
// userID
$userID = '';
if (isset($_SESSION['userID'])){
    $userID = $_SESSION['userID'];
   
}
?>


<?php
if (isset($_POST['btnSaveExpense'])){
    $categoryName = $_POST['categoryName'];
    $icon = $_POST['icon'];
    $necessityType = $_POST['necessityType'];
    $isFlexible = $_POST['limitTrack'];
    $targetLimit = $_POST['targetLimit'];
   

    $addExpenseCategoryQuery = "INSERT INTO `tbl_usercategories`(`categoryName`, `type`, `icon`, `userNecessityType`, `userisFlexible`, `defaultCategoryID`, `userID`, `isSelected`) 
    VALUES ('$categoryName','expense','$icon','$necessityType','$isFlexible','0','$userID','1')";
    executeQuery($addExpenseCategoryQuery);

    $lastUserCategoryID = mysqli_insert_id($conn);


    
    $addUserAllocationQuery = "INSERT INTO `tbl_userallocation`( `userBudgetruleID`, `userCategoryID`, `necessityType`, `limitType`, `value`) 
    VALUES ('[value-1]','$lastUserCategoryID','$necessityType','','[value-5]')";

    
    $_SESSION['successtag'] = $categoryName . ' added successfully!';
     header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  
}

?>