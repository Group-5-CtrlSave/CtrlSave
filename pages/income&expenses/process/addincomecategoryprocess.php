<?php
// userID
$userID = '';
if (isset($_SESSION['userID'])){
    $userID = $_SESSION['userID'];
   
}
?>

<?php
if (isset($_POST['addIncomeCategory'])){
    $categoryName = $_POST['categoryName'];
    $icon = $_POST['icon'];

    $addIncomeCategoryQuery = "INSERT INTO `tbl_usercategories`(`categoryName`, `type`, `icon`, `userNecessityType`, `userisFlexible`, `defaultCategoryID`, `userID`, `isSelected`) 
    VALUES ('$categoryName','income','$icon','unspecified','0','0','$userID','1')";
   
    executeQuery($addIncomeCategoryQuery);
    $_SESSION['successtag'] = "Added $categoryName Successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
    
}
?>