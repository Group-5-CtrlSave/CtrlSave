<?php
session_start();
include("../../assets/shared/connect.php");

$error = "";

// Ensure user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['setCurrency'])) {

    $currency = $_POST['currency'] ?? "";
    $userID   = (int) $_SESSION['userID'];

    if (!in_array($currency, ['PHP', 'USD'])) {
        $error = "Please choose a valid currency.";
    } else {
        $stmt = $conn->prepare("UPDATE tbl_users SET currencyCode = ? WHERE userID = ?");
        $stmt->bind_param("si", $currency, $userID);

        if ($stmt->execute()) {

            $_SESSION['currencyCode'] = $currency;

            header("Location: balance.php");
            exit;
        } else {
            $error = "Failed to save currency. Try again.";
        }
    }
}
?>
