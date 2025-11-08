<?php
session_start();

// NOTE: Assuming $conn is already defined and is a valid mysqli connection object

// Ensure logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit();
}

$error = "";
$userID = $_SESSION['userID'];

// Handle form submit
if (isset($_POST['submit'])) {

    $raw_balance = trim($_POST['balance']);

    // Data cleaning logic (removing symbols/formatting)
    $clean = str_replace(['₱', ',', ' '], '', $raw_balance);
    $clean = preg_replace('/[^\d.]/', '', $clean);

    // Validation logic
    if ($clean === "") {
        $error = "Please enter your starting balance.";
    } else if (!is_numeric($clean)) {
        $error = "Balance must be a valid number.";
    } else if (floatval($clean) <= 0) {
        $error = "Balance must be greater than 0.";
    } else {

        $balance = floatval($clean);

        // --- CORE DATABASE OPERATIONS (LOOKUP, UPDATE, INSERT) ---

        // 1. Find the active budget version ID (prepared by signupBE.php)
        $budgetStmt = $conn->prepare("SELECT userBudgetversionID FROM tbl_userbudgetversion WHERE userID = ? AND isActive = 1 LIMIT 1");
        $budgetStmt->bind_param("i", $userID);
        $budgetStmt->execute();
        $budgetResult = $budgetStmt->get_result();
        
        if ($budgetResult->num_rows === 0) {
            $error = "System error: Budget version not found.";
        } else {
            $budgetRow = $budgetResult->fetch_assoc();
            $userBudgetversionID = $budgetRow['userBudgetversionID'];
            $budgetStmt->close();

            // 2. Find the Allowance category ID (defaultCategoryID = 1, prepared by signupBE.php)
            $categoryStmt = $conn->prepare("SELECT userCategoryID FROM tbl_usercategories WHERE userID = ? AND defaultCategoryID = 1 LIMIT 1");
            $categoryStmt->bind_param("i", $userID);
            $categoryStmt->execute();
            $categoryResult = $categoryStmt->get_result();

            if ($categoryResult->num_rows === 0) {
                 $error = "System error: 'Allowance' category not found. Setup failed at signup.";
            } else {
                $categoryRow = $categoryResult->fetch_assoc();
                $allowanceCategoryID = $categoryRow['userCategoryID'];
                $categoryStmt->close();

                // 3. Update the existing budget version with the initial balance
                $updateBudgetStmt = $conn->prepare("UPDATE tbl_userbudgetversion SET balance = ? WHERE userBudgetversionID = ?");
                $updateBudgetStmt->bind_param("di", $balance, $userBudgetversionID);
                $updateSuccess = $updateBudgetStmt->execute();
                $updateBudgetStmt->close();

                // 4. Record the initial balance as an Income transaction into tbl_income
                $note = "Allowance";
                $insertIncomeStmt = $conn->prepare("
                    INSERT INTO tbl_income 
                    (userID, amount, note, categoryID, userBudgetversionID) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insertIncomeStmt->bind_param("idsii", $userID, $balance, $note, $allowanceCategoryID, $userBudgetversionID);
                $incomeSuccess = $insertIncomeStmt->execute();
                $insertIncomeStmt->close();

                // 5. Final check and redirect
                if ($updateSuccess && $incomeSuccess) {
                    // Redirect to the next step: pickExpense.php
                    header("Location: pickExpense.php");
                    exit();
                } else {
                    $error = "Something went wrong during the balance operation. Please try again.";
                }
            }
        }
    }
}
?>