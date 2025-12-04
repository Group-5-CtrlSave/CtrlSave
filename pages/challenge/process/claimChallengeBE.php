<?php
session_start();
include("../../../assets/shared/connect.php");

header("Content-Type: application/json; charset=UTF-8");

// 1. Validate POST
if (!isset($_POST['challengeID'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "No challenge ID"
    ]);
    exit;
}

$challengeID = intval($_POST['challengeID']);
$userID      = $_SESSION['userID'] ?? null;

if (!$userID) {
    echo json_encode([
        "status"  => "error",
        "message" => "Not logged in"
    ]);
    exit;
}

// 2. Check challenge exists, belongs to user, and get its type + base exp
$sql = "
    SELECT u.status, u.completedAt, c.exp, c.type
    FROM tbl_userchallenges u
    JOIN tbl_challenges c ON c.challengeID = u.challengeID
    WHERE u.userChallengeID = $challengeID
      AND u.userID = $userID
    LIMIT 1
";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Challenge not found"
    ]);
    exit;
}

$row  = mysqli_fetch_assoc($res);
$type = $row['type']; // exactly as stored in DB: 'daily' / 'weekly'

// 3. Must be completed before claiming
if ($row['status'] !== "completed") {
    echo json_encode([
        "status"  => "error",
        "message" => "Challenge not completed"
    ]);
    exit;
}

// 4. Decide EXP reward based on challenge type
//    Daily = 5 XP, Weekly = 20 XP, others = use c.exp as fallback
if ($type === 'daily') {
    $expReward = 5;
} elseif ($type === 'weekly') {
    $expReward = 20;
} else {
    $expReward = intval($row['exp']); // fallback to DB value
}

// 5. Mark claimed (do not touch completedAt)
$updateClaim = "
    UPDATE tbl_userchallenges
    SET status = 'claimed',
        claimedAt = NOW()
    WHERE userChallengeID = $challengeID
      AND userID = $userID
    LIMIT 1
";
mysqli_query($conn, $updateClaim);

// 6. Give EXP to the user
$updateExp = "
    UPDATE tbl_userlvl
    SET exp = exp + $expReward
    WHERE userID = $userID
";
mysqli_query($conn, $updateExp);

// 7. Fetch updated level info and handle level ups
$userRes = mysqli_query($conn, "
    SELECT lvl, exp
    FROM tbl_userlvl
    WHERE userID = $userID
    LIMIT 1
");

if (!$userRes || mysqli_num_rows($userRes) == 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "User level row missing"
    ]);
    exit;
}

$user      = mysqli_fetch_assoc($userRes);
$lvl       = intval($user['lvl']);
$exp       = intval($user['exp']);
$leveledUp = false;

// Level-up loop: cost per level = lvl * 100
while ($exp >= $lvl * 100) {
    $exp -= $lvl * 100;
    $lvl++;
    $leveledUp = true;
}

// 8. Save new level & remaining exp
mysqli_query($conn, "
    UPDATE tbl_userlvl
    SET lvl = $lvl,
        exp = $exp
    WHERE userID = $userID
");

// 9. Return JSON for frontend
echo json_encode([
    "status"    => "success",
    "exp"       => $expReward,
    "type"      => $type,       // 'daily' or 'weekly' from DB
    "leveledUp" => $leveledUp
]);
exit;
?>
