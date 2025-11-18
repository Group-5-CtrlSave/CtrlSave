<?php
session_start();

$error = "";

// Make sure user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['setCurrency'])) {

    $currency = $_POST['currency'] ?? "";
    $userID   = (int) $_SESSION['userID']; // type safety

    // Validate selection
    if ($currency !== "PHP" && $currency !== "USD") {
        $error = "Please choose a valid currency.";
    } else {

        // Escape currency for safety
        $currencyEsc = $conn->real_escape_string($currency);

        // Query version (no prepare)
        $updateSql = "
            UPDATE tbl_users 
            SET currencyCode = '$currencyEsc' 
            WHERE userID = $userID
        ";

        if ($conn->query($updateSql)) {
            header("Location: balance.php");
            exit();
        } else {
            $error = "Failed to save currency, try again.";
        }
    }
}
?>
