<?php
session_start();

$error = "";

try {
    if (!isset($_SESSION['userID'])) {
        throw new Exception("Session expired. Please log in again.");
    }

    $userID = (int) $_SESSION['userID'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['categories'])) {
            $error = "Please select at least one expense before proceeding.";
        } else {
            // Reset all selections
            $reset = $conn->prepare("UPDATE tbl_usercategories SET isSelected = 0 WHERE userID = ?");
            $reset->bind_param("i", $userID);
            $reset->execute();
            $reset->close();

            foreach ($_POST['categories'] as $catID) {
                $catID = (int)$catID;

                // Check whether this ID belongs to a user-added or default category
                $check = $conn->prepare("SELECT userCategoryID FROM tbl_usercategories WHERE userCategoryID = ? AND userID = ?");
                $check->bind_param("ii", $catID, $userID);
                $check->execute();
                $check->store_result();

                if ($check->num_rows > 0) {
                    // Existing user category (added manually)
                    $update = $conn->prepare("UPDATE tbl_usercategories SET isSelected = 1 WHERE userCategoryID = ? AND userID = ?");
                    $update->bind_param("ii", $catID, $userID);
                    $update->execute();
                    $update->close();
                } else {
                    // Otherwise treat as a default category
                    $insert = $conn->prepare("
                        INSERT INTO tbl_usercategories 
                        (categoryName, type, icon, userNecessityType, userisFlexible, defaultCategoryID, userID, isSelected)
                        SELECT categoryName, type, icon, defaultNecessitytype, defaultIsflexible, defaultCategoryID, ?, 1
                        FROM tbl_defaultcategories WHERE defaultCategoryID = ?
                    ");
                    $insert->bind_param("ii", $userID, $catID);
                    $insert->execute();
                    $insert->close();
                }
                $check->close();
            }

            if (empty($error)) {
                header("Location: needsWants.php");
                exit;
            }
        }
    }

    // Fetch default categories (base list)
    $defaultStmt = $conn->prepare("SELECT * FROM tbl_defaultcategories WHERE type = 'expense' ORDER BY categoryName ASC");
    $defaultStmt->execute();
    $defaultResult = $defaultStmt->get_result();

    // Fetch all user categories — NEW ONES have NULL defaultCategoryID
    $userStmt = $conn->prepare("
        SELECT * FROM tbl_usercategories 
        WHERE type = 'expense' AND userID = ? 
        ORDER BY userCategoryID DESC
    ");
    $userStmt->bind_param("i", $userID);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>