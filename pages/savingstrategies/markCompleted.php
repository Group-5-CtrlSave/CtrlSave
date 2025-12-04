<?php
session_start();
header('Content-Type: application/json');
include_once '../../assets/shared/connect.php';

if (!isset($_SESSION['userID']) || !isset($_POST['resourceID'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit();
}

$userID = (int)$_SESSION['userID'];
$resourceID = (int)$_POST['resourceID'];

// ensure resource exists (and get resourceType)
$chkRes = $conn->prepare("SELECT resourceID, resourceType FROM tbl_resources WHERE resourceID = ?");
$chkRes->bind_param("i", $resourceID);
$chkRes->execute();
$res = $chkRes->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Resource not found']);
    exit();
}

$resource = $res->fetch_assoc();

include_once "../challenge/process/challengeController.php";

switch (strtolower($resource['resourceType'])) {
    case 'video':
        updateSavingVideoChallenge($userID, $conn);
        break;

    case 'article':
        updateSavingArticleChallenge($userID, $conn);
        break;
}

// check if already marked
$check = $conn->prepare("SELECT id FROM tbl_user_resource_progress WHERE userID = ? AND resourceID = ?");
$check->bind_param("ii", $userID, $resourceID);
$check->execute();
$checkRes = $check->get_result();

if ($checkRes->num_rows == 0) {
    $insert = $conn->prepare("
        INSERT INTO tbl_user_resource_progress (userID, resourceID, isCompleted, dateCompleted)
        VALUES (?, ?, 1, NOW())
    ");
    $insert->bind_param("ii", $userID, $resourceID);
    $insert->execute();
}

// return updated completion percentage
$totalQ = $conn->query("SELECT COUNT(*) AS total FROM tbl_resources");
$total = (int)$totalQ->fetch_assoc()['total'];

$completedQ = $conn->prepare("SELECT COUNT(*) AS completed FROM tbl_user_resource_progress WHERE userID = ?");
$completedQ->bind_param("i", $userID);
$completedQ->execute();
$completedRes = $completedQ->get_result();
$completed = (int)$completedRes->fetch_assoc()['completed'];

$percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

echo json_encode(['success' => true, 'percentage' => $percentage]);
exit();
