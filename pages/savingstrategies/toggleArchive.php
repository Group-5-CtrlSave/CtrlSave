<?php
session_start();
include_once '../../assets/shared/connect.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false]);
    exit();
}

$userID = $_SESSION['userID'];
$resourceID = (int) $_POST['resourceID'];

// Check if row exists
$checkQuery = "SELECT isArchived FROM tbl_user_resource_progress WHERE userID = $userID AND resourceID = $resourceID";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    $row = mysqli_fetch_assoc($checkResult);
    $newArchived = 1 - $row['isArchived'];
    $updateQuery = "UPDATE tbl_user_resource_progress SET isArchived = $newArchived WHERE userID = $userID AND resourceID = $resourceID";
    $success = mysqli_query($conn, $updateQuery);
} else {
    $insertQuery = "INSERT INTO tbl_user_resource_progress (userID, resourceID, isCompleted, isFavorited, isArchived) VALUES ($userID, $resourceID, 0, 0, 1)";
    $success = mysqli_query($conn, $insertQuery);
}

echo json_encode(['success' => (bool) $success]);
?>