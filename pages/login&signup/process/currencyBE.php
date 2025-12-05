<?php
session_start();
include("../../assets/shared/connect.php");

$error = "";

// Ensure user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['setCurrency'])) {

    $currency = $_POST['currency'] ?? "";
    $userID   = (int) $_SESSION['userID'];

    // Validate option
    if ($currency !== "PHP" && $currency !== "USD") {
        $error = "Please choose a valid currency.";
    } else {

        // Prepared statement for security
        $stmt = $conn->prepare("
            UPDATE tbl_users
            SET currencyCode = ?
            WHERE userID = ?
        ");
        $stmt->bind_param("si", $currency, $userID);

        if ($stmt->execute()) {
            header("Location: balance.php");
            exit();
        } else {
            $error = "Failed to save currency. Try again.";
        }
    }
}
?>
