<?php
session_start();

// NOTE: Assuming $conn is already defined and is a valid mysqli connection object

$error = "";

if (isset($_POST['signup'])) {

    $username = trim($_POST['username']);
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ... (Your existing email check logic remains here) ...
    $check = $conn->prepare("SELECT email FROM tbl_users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult && $checkResult->num_rows > 0) {
        $error = "Email already registered.";
    } else {

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $insert = $conn->prepare("INSERT INTO tbl_users (userName, firstName, lastName, email, password) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $username, $fname, $lname, $email, $hashedPassword);

        if ($insert->execute()) {

            $newUserID = $insert->insert_id;
            $_SESSION['userID'] = $newUserID;
            
            // --- DATABASE INITIALIZATION START ---

            // The three income categories to auto-insert, with assumed defaultCategoryID (1, 2, 3)
            $incomeCategories = [
                // CategoryName, Type, Icon, defaultCategoryID
                ['Allowance', 'income', 'Allowance.png', 1],
                ['Income',    'income', 'Income.png',    2],
                ['Scholarship', 'income', 'Scholarship.png', 3],
            ];
            
            // Prepare the INSERT statement for user categories
            $categoryInsert = $conn->prepare("
                INSERT INTO tbl_usercategories 
                (userID, categoryName, type, icon, userNecessityType, userisFlexible, defaultCategoryID, isSelected) 
                VALUES (?, ?, ?, ?, 'unspecified', 0, ?, 1)
            ");
            
            // Loop through each income category and insert it
            $success = true;
            foreach ($incomeCategories as $cat) {
                // $cat[0]=Name, $cat[1]=Type, $cat[2]=Icon, $cat[3]=defaultCategoryID
                $categoryInsert->bind_param(
                    "isssi", 
                    $newUserID, 
                    $cat[0], // CategoryName
                    $cat[1], // Type (income)
                    $cat[2], // Icon
                    $cat[3]  // defaultCategoryID
                );
                if (!$categoryInsert->execute()) {
                    $success = false;
                    // In a real application, you'd log this error and potentially roll back the user creation.
                    break; 
                }
            }
            $categoryInsert->close();

            // Prepare the budget version (crucial for balanceBE.php)
            $initialBalance = 0.00;
            $insertBudget = $conn->prepare("INSERT INTO tbl_userbudgetversion (userID, balance, isActive) VALUES (?, ?, 1)");
            $insertBudget->bind_param("id", $newUserID, $initialBalance);
            $budgetSuccess = $insertBudget->execute();
            $insertBudget->close();


            // --- DATABASE INITIALIZATION END ---

            if ($success && $budgetSuccess) {
                // Redirect to currency page after sign up
                header("Location: currency.php");
                exit();
            } else {
                // Handle a full or partial setup failure
                $error = "User registered, but initial budget setup failed. Please contact support.";
            }
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>