<?php
session_start();

include("../../assets/shared/connect.php");

// Make sure user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit();
}

$error = "";
$userID = (int) $_SESSION['userID'];
// $conn assumed existing

// Handle form submit
if (isset($_POST['submit'])) {

    $raw_balance = trim($_POST['balance']);

    // Clean formatting
    $clean = str_replace(['₱', ',', ' '], '', $raw_balance);
    $clean = preg_replace('/[^\d.]/', '', $clean);

    // Validation
    if ($clean === "") {
        $error = "Please enter your starting balance.";
    } else if (!is_numeric($clean)) {
        $error = "Balance must be a valid number.";
    } else if (floatval($clean) <= 0) {
        $error = "Balance must be greater than 0.";
    } else {

        $balance = floatval($clean);

        // Get Active Budget Version
        $sqlBudget = "
            SELECT userBudgetversionID 
            FROM tbl_userbudgetversion 
            WHERE userID = $userID AND isActive = 1 
            LIMIT 1
        ";

        $rBudget = $conn->query($sqlBudget);

        if (!$rBudget || $rBudget->num_rows === 0) {
            $error = "System error: Budget version not found.";
        } else {

            $budgetRow = $rBudget->fetch_assoc();
            $userBudgetversionID = (int)$budgetRow['userBudgetversionID'];

            // Get Allowance Category 
            $sqlCategory = "
                SELECT userCategoryID 
                FROM tbl_usercategories 
                WHERE userID = $userID AND defaultCategoryID = 1
                LIMIT 1
            ";
            $rCategory = $conn->query($sqlCategory);

            if (!$rCategory || $rCategory->num_rows === 0) {
                $error = "System error: 'Allowance' category not found. Setup failed at signup.";
            } else {

                $catRow = $rCategory->fetch_assoc();
                $allowanceCategoryID = (int)$catRow['userCategoryID'];

                // Update budget version balance
                $balanceEsc = $conn->real_escape_string($balance);

                $updateSql = "
                    UPDATE tbl_userbudgetversion 
                    SET totalIncome = $balanceEsc 
                    WHERE userBudgetversionID = $userBudgetversionID
                ";

                $updateSuccess = $conn->query($updateSql);

                // Insert income transaction
                $note = $conn->real_escape_string("Allowance");

                $insertIncomeSql = "
                    INSERT INTO tbl_income 
                    (userID, amount, note, userCategoryID, userBudgetversionID)
                    VALUES (
                        $userID,
                        $balanceEsc,
                        '$note',
                        $allowanceCategoryID,
                        $userBudgetversionID
                    )
                ";

                $incomeSuccess = $conn->query($insertIncomeSql);

                if ($updateSuccess && $incomeSuccess) {
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
