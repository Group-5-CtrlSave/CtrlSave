<?php
// userID
$userID = '';
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
}
?>

<?php 
// Get all default categories
$getAllDefaultCategories = "
    SELECT 
        `defaultCategoryID` AS categoryID,
        `categoryName`,
        `icon`,
        `defaultNecessitytype` AS necessityType,
        `defaultIsflexible` AS isFlexible,
        'default' AS source
    FROM `tbl_defaultcategories`
    WHERE type = 'expense'
";
$defaultCategoriesResult = executeQuery($getAllDefaultCategories);
$defaultCategories = [];
if (mysqli_num_rows($defaultCategoriesResult) > 0) {
    while ($defaultCategory = mysqli_fetch_assoc($defaultCategoriesResult)) {
        $defaultCategories[] = $defaultCategory;
    }
}

// Get all user categories
$getAllUserCategories = "
    SELECT 
        `userCategoryID` AS categoryID,
        `categoryName`,
        `type`,
        `icon`,
        `userNecessityType` AS necessityType,
        `userisFlexible` AS isFlexible,
        'user' AS source,
        isSelected
    FROM `tbl_usercategories`
    WHERE type = 'expense' AND isSelected = 1 AND userID = $userID
";
$userCategoriesResult = executeQuery($getAllUserCategories);
$userCategories = [];
if (mysqli_num_rows($userCategoriesResult) > 0) {
    while ($userCategory = mysqli_fetch_assoc($userCategoriesResult)) {
        $userCategories[] = $userCategory;
    }
}

$allCategories = array_merge($userCategories, $defaultCategories);
?>

<?php
if (isset($_POST['btnSaveCategories'])) {

    $selected = $_POST['userCategories'] ?? []; // array of selected category IDs

    foreach ($allCategories as $category) {
        $catID = $category['categoryID'];
        $isSelected = in_array($catID, $selected) ? 1 : 0;

        if ($category['source'] === 'user') {
            // Update existing user category
            executeQuery("UPDATE tbl_usercategories SET isSelected=$isSelected WHERE userCategoryID=$catID");
        } else {
            // Default category: insert only if selected
            if ($isSelected) {
                // Check if it already exists for this user
                $check = executeQuery("SELECT * FROM tbl_usercategories WHERE userID=$userID AND categoryName='" . $category['categoryName'] . "'");
                
                if (mysqli_num_rows($check) == 0) {
                    // ✅ FIXED: include defaultCategoryID
                    executeQuery("
                        INSERT INTO tbl_usercategories 
                            (categoryName, type, icon, userID, isSelected, userNecessityType, userisFlexible, defaultCategoryID) 
                        VALUES 
                            ('" . $category['categoryName'] . "', 
                             'expense', 
                             '" . $category['icon'] . "', 
                             $userID, 
                             1, 
                             '" . $category['necessityType'] . "', 
                             '" . $category['isFlexible'] . "', 
                             " . $category['categoryID'] . ")
                    ");
                } else {
                    // Already exists: just update isSelected
                    executeQuery("UPDATE tbl_usercategories SET isSelected=1 WHERE userID=$userID AND categoryName='" . $category['categoryName'] . "'");
                }
            } else {
                // Unselect if exists
                executeQuery("UPDATE tbl_usercategories SET isSelected=0 WHERE userID=$userID AND categoryName='" . $category['categoryName'] . "'");
            }
        }
    }

    // Redirect back to PickExpense page
    header("Location: needsWants.php");
    exit;
}
?>
