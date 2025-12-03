<?php
session_start();
include("../../../assets/shared/connect.php");

header('Content-Type: text/plain');

// 1. Validate POST
if (!isset($_POST['challengeID']) || empty($_POST['challengeID'])) {
    echo "error: no challengeID provided";
    exit;
}

$challengeID = intval($_POST['challengeID']);
$userID = $_SESSION['userID'] ?? null;

if (!$userID) {
    echo "error: not logged in";
    exit;
}

// 2. Validate correct challenge + ownership
$checkQuery = "
    SELECT status, completedAt
    FROM tbl_userchallenges
    WHERE userChallengeID = $challengeID
      AND userID = $userID
    LIMIT 1
";
$checkResult = mysqli_query($conn, $checkQuery);

if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
    echo "error: challenge not found";
    exit;
}

$row = mysqli_fetch_assoc($checkResult);

// 3. Must be completed before claim
if ($row['status'] !== 'completed') {
    echo "error: challenge not completed";
    exit;
}

// 4. Must have a completedAt value already
$completedAt = $row['completedAt'];
if (!$completedAt || $completedAt == "0000-00-00 00:00:00") {
    echo "error: completedAt missing";
    exit;
}

// 5. Update claim ONLY (do not overwrite completedAt)
$updateQuery = "
    UPDATE tbl_userchallenges
    SET status = 'claimed',
        claimedAt = NOW()
    WHERE userChallengeID = $challengeID
      AND userID = $userID
    LIMIT 1
";

$updateResult = mysqli_query($conn, $updateQuery);

echo $updateResult ? "success" : "error: could not update";
?>
