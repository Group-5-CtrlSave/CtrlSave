<?php
session_start();
include("../../../assets/shared/connect.php");
include("challengeController.php");
include("savingChallengeFunctions.php"); // Required for new challenge creation

// ---------------------------
// USER VALIDATION
// ---------------------------
if (!isset($_SESSION['userID'])) {
    echo json_encode(["status" => "no-user"]);
    exit();
}

$userID = intval($_SESSION['userID']);

// ---------------------------
// POST VALIDATION
// ---------------------------
if (!isset($_POST['index']) || !isset($_POST['amount'])) {
    echo json_encode(["status" => "invalid"]);
    exit();
}

$index = intval($_POST['index']);
$amount = intval($_POST['amount']);

// ---------------------------
// 1. INSERT OR UPDATE SLOT PROGRESS
// ---------------------------
$stmt = $conn->prepare("
    INSERT INTO tbl_savingchallenge_progress (userID, itemIndex, amount, dateAdded)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE amount = VALUES(amount), dateAdded = NOW()
");
$stmt->bind_param("iii", $userID, $index, $amount);
$stmt->execute();

// ---------------------------
// ADD TO currentAmount IN ACTIVE CHALLENGE
// ---------------------------
$conn->query("
    UPDATE tbl_usersavingchallenge
    SET currentAmount = currentAmount + $amount
    WHERE userID = $userID AND status = 'active'
");

// ---------------------------
// FETCH ACTIVE CHALLENGE
// ---------------------------
$challengeQuery = $conn->query("
    SELECT * FROM tbl_usersavingchallenge
    WHERE userID = $userID AND status = 'active'
    LIMIT 1
");

if (!$challengeQuery || $challengeQuery->num_rows == 0) {
    echo json_encode(["status" => "error", "msg" => "No active challenge"]);
    exit();
}

$challenge = $challengeQuery->fetch_assoc();

$current = intval($challenge['currentAmount']);
$target = intval($challenge['targetAmount']);
$rewardEXP = intval($challenge['expReward']);

// ---------------------------
// COMPLETION CHECK
// ---------------------------
if ($current >= $target) {

    // Mark current challenge completed
    $conn->query("
        UPDATE tbl_usersavingchallenge
        SET status = 'completed', completedAt = NOW()
        WHERE userSavingChallengeID = {$challenge['userSavingChallengeID']}
    ");

    // Award EXP
    $conn->query("
        UPDATE tbl_userlvl
        SET exp = exp + $rewardEXP
        WHERE userID = $userID
    ");

    // Re-fetch updated exp + level
    $row = $conn->query("
        SELECT lvl, exp FROM tbl_userlvl WHERE userID = $userID
    ")->fetch_assoc();

    $lvl = intval($row['lvl']);
    $exp = intval($row['exp']);

    // LEVEL-UP LOOP
    while ($exp >= $lvl * 100) {
        $exp -= ($lvl * 100);
        $lvl++;
    }

    // Update level + remaining exp
    $conn->query("
        UPDATE tbl_userlvl
        SET lvl = $lvl, exp = $exp
        WHERE userID = $userID
    ");

    // Clear old progress (the 20-slot grid)
    $conn->query("
        DELETE FROM tbl_savingchallenge_progress
        WHERE userID = $userID
    ");

    // Create next challenge at new level
    createSavingChallenge($conn, $userID, $lvl);         

    echo json_encode([               
        "status" => "completed",
        "expAwarded" => $rewardEXP,
        "newLevel" => $lvl
    ]);
    exit();
}
                                              
// ---------------------------
// NOT COMPLETED → UPDATE DAILY/WEEKLY CHALLENGES
// ---------------------------
updateSavingDaily10Peso($userID, $conn);
updateSavingWeeklyRow($userID, $conn);

// Respond OK
echo json_encode(["status" => "ok"]);
exit();
?>
