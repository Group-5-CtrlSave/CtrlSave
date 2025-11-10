<?php
session_start();

$error = "";

try {
    if (!isset($_SESSION['userID'])) {
        throw new Exception("Session expired. Please log in again.");
    }

    $userID = (int) $_SESSION['userID'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Sanitize inputs
        $categoryName = trim($_POST['categoryName'] ?? '');
        $selectedIcon = trim($_POST['selectedIcon'] ?? '');
        $needWant = strtolower(trim($_POST['category'] ?? '')); // store as 'need' or 'want'
        $limitTrack = trim($_POST['limits'] ?? '');

        // --- Validation ---
        if ($categoryName === '' || $selectedIcon === '' || $needWant === '' || $limitTrack === '') {
            $error = "Please fill in all fields before saving.";
        } else {
            // --- Check duplicate ---
            $check = $conn->prepare("
                SELECT userCategoryID 
                FROM tbl_usercategories 
                WHERE categoryName = ? AND userID = ?
            ");
            if (!$check) {
                throw new Exception("Database error: " . $conn->error);
            }

            $check->bind_param("si", $categoryName, $userID);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "This expense category already exists.";
            } else {
                // --- Prepare insert ---
                $limitTrack = (int)$limitTrack; // 1 = Limit, 0 = Track

                $insert = $conn->prepare("
                    INSERT INTO tbl_usercategories
                    (categoryName, type, icon, userNecessityType, userisFlexible, userID, isSelected)
                    VALUES (?, 'expense', ?, ?, ?, ?, 1)
                ");

                if (!$insert) {
                    throw new Exception("Failed to prepare insert statement: " . $conn->error);
                }

                $insert->bind_param("sssii", $categoryName, $selectedIcon, $needWant, $limitTrack, $userID);

                if (!$insert->execute()) {
                    throw new Exception("Failed to save new expense category: " . $insert->error);
                }

                // ✅ Close insert before redirect
                $insert->close();
                $check->close();

                // Save new category ID (optional, can use later)
                $_SESSION['newlyAddedExpenseCatID'] = $conn->insert_id;

                // Redirect back to pickExpense page
                header("Location: pickExpense.php");
                exit;
            }

            // ✅ Always close after usage
            $check->close();
        }
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
