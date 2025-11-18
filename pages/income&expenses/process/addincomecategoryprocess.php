<?php
if (isset($_POST['addIncomeCategory'])){
    $categoryName = $_POST['categoryName'];
    $icon = $_POST['icon'];

    $addIncomeCategoryQuery = "INSERT INTO `tbl_usercategories`(`categoryName`, `type`, `icon`, `userNecessityType`, `userisFlexible`, `defaultCategoryID`, `userID`, `isSelected`) 
    VALUES ('$categoryName','income','$icon','unspecified','0','0','1','1')";
    executeQuery($addIncomeCategoryQuery);
}
?>