<?php
session_start();
include("../../assets/shared/connect.php");

$userID = $_SESSION['userID'] ?? 0;
if (!$userID) {
    echo json_encode(["daily" => [], "weekly" => []]);
    exit;
}

// DAILY
$daily = [];
$q1 = $conn->query("
    SELECT c.challengeName, u.userChallengeID, u.status, u.assignedDate
    FROM tbl_challenges c
    JOIN tbl_userchallenges u ON c.challengeID = u.challengeID
    WHERE u.userID = $userID AND c.type='Daily'
      AND u.status IN ('in progress', 'completed')
");
while ($row = $q1->fetch_assoc()) $daily[] = $row;

// WEEKLY
$weekly = [];
$q2 = $conn->query("
    SELECT c.challengeName, u.userChallengeID, u.status, u.assignedDate
    FROM tbl_challenges c
    JOIN tbl_userchallenges u ON c.challengeID = u.challengeID
    WHERE u.userID = $userID AND c.type='Weekly'
      AND u.status IN ('in progress', 'completed')
");
while ($row = $q2->fetch_assoc()) $weekly[] = $row;

echo json_encode(["daily" => $daily, "weekly" => $weekly]);
?>
