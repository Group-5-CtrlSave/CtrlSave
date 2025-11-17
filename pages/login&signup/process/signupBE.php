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

        // Start Transaction to ensure User AND Categories are created together
        $conn->begin_transaction();
        $success = false; // Initialize flag for transaction status

        try {
            // 1. Insert user
            $insert = $conn->prepare("INSERT INTO tbl_users (userName, firstName, lastName, email, password) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("sssss", $username, $fname, $lname, $email, $hashedPassword);

            if (!$insert->execute()) {
                throw new Exception("User insertion failed.");
            }

            $newUserID = $insert->insert_id;
            $_SESSION['userID'] = $newUserID;
            $insert->close();
            
            // --- 2. DATABASE INITIALIZATION START: Income and Savings ---

            // The income categories to auto-insert
            $categoriesToInsert = [
                // CategoryName, Type, Icon, defaultCategoryID, userNecessityType
                ['Allowance', 'income', 'Allowance.png', 1, 'income'],
                ['Income',    'income', 'Income.png',    2, 'income'],
                ['Scholarship', 'income', 'Scholarship.png', 3, 'income'],
            ];
            
            // --- A. Get the defaultCategoryID for 'Savings' from system table ---
            $sql_get_savings_default = "SELECT defaultCategoryID, icon FROM tbl_defaultCategories WHERE categoryName = 'Savings' AND type = 'savings'";
            $result_savings_default = $conn->query($sql_get_savings_default);
            $savings_default_row = $result_savings_default->fetch_assoc();

            if (!$savings_default_row) {
                // Critical failure if system defaults are missing 'Savings'
                throw new Exception("CRITICAL: Default 'Savings' category not found in system defaults.");
            }

            // Add Savings to the list of categories to insert
            $categoriesToInsert[] = [
                'Savings', 
                'savings', 
                $savings_default_row['icon'], 
                $savings_default_row['defaultCategoryID'], 
                'saving' // Use 'saving' or 'fixed' as the NecessityType for savings
            ];

            // --- B. Prepare and Execute the INSERT statement for user categories ---
            $categoryInsert = $conn->prepare("
                INSERT INTO tbl_usercategories 
                (userID, categoryName, type, icon, userNecessityType, userisFlexible, defaultCategoryID, isSelected) 
                VALUES (?, ?, ?, ?, ?, 0, ?, 1)
            ");
            
            foreach ($categoriesToInsert as $cat) {
                // $cat[0]=Name, $cat[1]=Type, $cat[2]=Icon, $cat[3]=defaultCategoryID, $cat[4]=userNecessityType
                $categoryInsert->bind_param(
                    "issssi", 
                    $newUserID, 
                    $cat[0], // CategoryName
                    $cat[1], // Type (income or savings)
                    $cat[2], // Icon
                    $cat[4], // userNecessityType (income or saving)
                    $cat[3]  // defaultCategoryID
                );
                if (!$categoryInsert->execute()) {
                    throw new Exception("Category insertion failed: " . $conn->error);
                }
            }
            $categoryInsert->close();

            // --- C. Prepare the budget version (essential for balance tracking) ---
            $initialBalance = 0.00;
            $insertBudget = $conn->prepare("INSERT INTO tbl_userbudgetversion (userID, balance, isActive) VALUES (?, ?, 1)");
            $insertBudget->bind_param("id", $newUserID, $initialBalance);
            if (!$insertBudget->execute()) {
                 throw new Exception("Budget version insertion failed: " . $conn->error);
            }
            $insertBudget->close();


            // --- DATABASE INITIALIZATION END ---
            
            // Commit if everything worked
            $conn->commit();
            $success = true;

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Setup failed: " . $e->getMessage();
        }

        if ($success) {
            // Redirect to currency page after sign up
            header("Location: currency.php");
            exit();
        } else {
            // The error variable is already set in the catch block
            // You can add logic here to display $error to the user
        }
    }
}
?>