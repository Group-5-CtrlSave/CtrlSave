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

// Handle form submission
if (isset($_POST['submit'])) {

    $raw_balance = trim($_POST['balance']);

    // Clean formatting
    $clean = str_replace(['₱', '$', ',', ' '], '', $raw_balance);
    $clean = preg_replace('/[^\d.]/', '', $clean);

    // Validation
    if ($clean === "") {
        $error = "Please enter your starting balance.";
    } 
    else if (!is_numeric($clean)) {
        $error = "Balance must be a valid number.";
    } 
    else if (floatval($clean) <= 0) {
        $error = "Balance must be greater than 0.";
    } 
    else {

        $balance = floatval($clean);

        // ======================================================
        // STEP 1: CHECK if Active Budget Version Already Exists
        // ======================================================
        $sqlBudget = "
            SELECT userBudgetversionID 
            FROM tbl_userbudgetversion 
            WHERE userID = $userID AND isActive = 1
            LIMIT 1
        ";

        $rBudget = $conn->query($sqlBudget);

        if (!$rBudget || $rBudget->num_rows === 0) {

            // ======================================================
            // NO VERSION EXISTS → CREATE VERSION 1 NOW
            // userBudgetRuleID = 0 (placeholder until user chooses)
            // ======================================================
            $stmt = $conn->prepare("
                INSERT INTO tbl_userbudgetversion
                (versionNumber, userID, userBudgetRuleID, totalIncome, isActive)
                VALUES (1, ?, 0, ?, 1)
            ");
            $stmt->bind_param("id", $userID, $balance);
            $stmt->execute();
            $userBudgetversionID = $stmt->insert_id;
            $stmt->close();

        } else {

            // Version already exists → use it
            $budgetRow = $rBudget->fetch_assoc();
            $userBudgetversionID = (int) $budgetRow['userBudgetversionID'];

            // Update income inside that version
            $stmt = $conn->prepare("
                UPDATE tbl_userbudgetversion
                SET totalIncome = ?
                WHERE userBudgetversionID = ?
            ");
            $stmt->bind_param("di", $balance, $userBudgetversionID);
            $stmt->execute();
            $stmt->close();
        }


        // ======================================================
        // STEP 2: GET ALLOWANCE CATEGORY
        // defaultCategoryID = 1 (Allowance)
        // ======================================================
        $sqlCategory = "
            SELECT userCategoryID
            FROM tbl_usercategories
            WHERE userID = $userID AND defaultCategoryID = 1
            LIMIT 1
        ";

        $rCategory = $conn->query($sqlCategory);

        if (!$rCategory || $rCategory->num_rows === 0) {
            $error = "System error: 'Allowance' category was not created at signup.";
        } 
        else {

            $catRow = $rCategory->fetch_assoc();
            $allowanceCategoryID = (int) $catRow['userCategoryID'];

            // ======================================================
            // STEP 3: INSERT STARTING BALANCE AS INCOME
            // ======================================================
            $note = "Allowance";

            $stmt = $conn->prepare("
                INSERT INTO tbl_income 
                (userID, amount, note, userCategoryID, userBudgetversionID)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("idsii", 
                $userID, 
                $balance, 
                $note, 
                $allowanceCategoryID, 
                $userBudgetversionID
            );
            $incomeSuccess = $stmt->execute();
            $stmt->close();


            // ======================================================
            // STEP 4: SUCCESS → Redirect to next onboarding page
            // ======================================================
            if ($incomeSuccess) {
                header("Location: pickExpense.php");
                exit();
            } else {
                $error = "Error inserting income. Please try again.";
            }
        }
    }
}
?>
