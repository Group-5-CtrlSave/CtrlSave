<?php
session_start();
include("../../../assets/shared/connect.php");
include("challengeController.php");

// Validate user
if (!isset($_SESSION['userID'])) {
    echo "no-user";
    exit();
}

$userID = intval($_SESSION['userID']);

// Validate POST
if (!isset($_POST['index']) || !isset($_POST['amount'])) {
    echo "invalid";
    exit();
}

$index = intval($_POST['index']);
$amount = intval($_POST['amount']);

// Safe query
$stmt = $conn->prepare("
    INSERT INTO tbl_savingchallenge_progress (userID, itemIndex, amount, dateAdded)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE amount = VALUES(amount), dateAdded = NOW()
");

$stmt->bind_param("iii", $userID, $index, $amount);
$stmt->execute();

// Update daily/weekly challenges
updateSavingDaily10Peso($userID, $conn);
updateSavingWeeklyRow($userID, $conn);

echo "ok";
exit();
?>
