<?php
session_start();
include("../../../assets/shared/connect.php");

header("Content-Type: application/json; charset=UTF-8");

// Set MySQL session timezone to Manila
$conn->query("SET time_zone = '+08:00'");

// 1. Validate POST
if (!isset($_POST['challengeID'])) {
    echo json_encode(["status" => "error", "message" => "No challenge ID"]);
    exit;
}

$challengeID = intval($_POST['challengeID']);
$userID = $_SESSION['userID'] ?? null;

if (!$userID) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

// 2. Fetch challenge info
$sql = "
    SELECT u.status, c.exp, c.type
    FROM tbl_userchallenges u
    JOIN tbl_challenges c ON c.challengeID = u.challengeID
    WHERE u.userChallengeID = $challengeID
      AND u.userID = $userID
    LIMIT 1
";

$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    echo json_encode(["status" => "error", "message" => "Challenge not found"]);
    exit;
}

$row = mysqli_fetch_assoc($res);
$type = strtolower($row['type']); // "daily" or "weekly"

// 3. Must be completed
if ($row['status'] !== "completed") {
    echo json_encode(["status" => "error", "message" => "Challenge not completed"]);
    exit;
}

// 4. Fixed EXP reward rules
if ($type === "daily") {
    $expReward = 5;
} elseif ($type === "weekly") {
    $expReward = 20;
} else {
    $expReward = 0; // No other type should give EXP
}

// 5. Mark claimed
mysqli_query($conn, "
    UPDATE tbl_userchallenges
    SET status = 'claimed', claimedAt = NOW()
    WHERE userChallengeID = $challengeID AND userID = $userID
");

// 6. Add EXP to user
mysqli_query($conn, "
    UPDATE tbl_userlvl
    SET exp = exp + $expReward
    WHERE userID = $userID
");

// 7. Get updated level
$userRes = mysqli_query($conn, "
    SELECT lvl, exp FROM tbl_userlvl WHERE userID = $userID LIMIT 1
");

$user = mysqli_fetch_assoc($userRes);
$lvl = intval($user['lvl']);
$exp = intval($user['exp']);
$leveledUp = false;

function xpRequired($level)
{
    return 100 + (($level - 1) * 20);
}

while ($exp >= xpRequired($lvl)) {
    $exp -= xpRequired($lvl);
    $lvl++;
    $leveledUp = true;
}

// Save updated level + exp
mysqli_query($conn, "
    UPDATE tbl_userlvl
    SET lvl = $lvl, exp = $exp
    WHERE userID = $userID
");

// Return success
echo json_encode([
    "status" => "success",
    "exp" => $expReward,
    "type" => $type,
    "leveledUp" => $leveledUp
]);
exit;
?>