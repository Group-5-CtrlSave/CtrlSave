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
    $userID = $_SESSION['userID'];

    if ($currency !== "PH" && $currency !== "US") {
        $error = "Please choose a valid currency.";
    } else {
        $update = $conn->prepare("UPDATE tbl_users SET currencyCode = ? WHERE userID = ?");
        $update->bind_param("si", $currency, $userID);

        if ($update->execute()) {
            header("Location: balance.php");
            exit();
        } else {
            $error = "Failed to save currency, try again.";
        }
    }
}
?>