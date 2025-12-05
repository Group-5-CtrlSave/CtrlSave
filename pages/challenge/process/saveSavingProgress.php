<?php
session_start();
include("../../../assets/shared/connect.php");
include("challengeController.php");
include("savingChallengeFunctions.php");

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
// 1. SAVE SLOT PROGRESS
// ---------------------------
$stmt = $conn->prepare("
    INSERT INTO tbl_savingchallenge_progress (userID, itemIndex, amount, dateAdded)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE amount = VALUES(amount), dateAdded = NOW()
");
$stmt->bind_param("iii", $userID, $index, $amount);
$stmt->execute();

// ---------------------------
// 2. ADD TO CURRENT AMOUNT
// ---------------------------
$conn->query("
    UPDATE tbl_usersavingchallenge
    SET currentAmount = currentAmount + $amount
    WHERE userID = $userID AND status = 'active'
");

// ---------------------------
// 3. FETCH ACTIVE CHALLENGE
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

// ---------------------------
// 4. COMPLETION CHECK
// ---------------------------
if ($current >= $target) {

    // Mark as completed
    $conn->query("
        UPDATE tbl_usersavingchallenge
        SET status = 'completed', completedAt = NOW()
        WHERE userSavingChallengeID = {$challenge['userSavingChallengeID']}
    ");

    // FIXED SAVING CHALLENGE REWARD → ALWAYS 50 XP
    $rewardEXP = 50;

    // Update EXP
    $conn->query("
        UPDATE tbl_userlvl
        SET exp = exp + $rewardEXP
        WHERE userID = $userID
    ");

    // Fetch updated user level
    $row = $conn->query("
        SELECT lvl, exp FROM tbl_userlvl WHERE userID = $userID
    ")->fetch_assoc();

    $lvl = intval($row['lvl']);
    $exp = intval($row['exp']);
    $leveledUp = false;

    // XP needed formula: Level 1=100, Level 2=120, Level 3=140...
    function xpRequired($level)
    {
        return 100 + (($level - 1) * 20);
    }

    while ($exp >= xpRequired($lvl)) {
        $exp -= xpRequired($lvl);
        $lvl++;
        $leveledUp = true;
    }


    // Save level state
    $conn->query("
        UPDATE tbl_userlvl
        SET lvl = $lvl, exp = $exp
        WHERE userID = $userID
    ");

    // Clear used slot progress
    $conn->query("
        DELETE FROM tbl_savingchallenge_progress
        WHERE userID = $userID
    ");

    // Create NEW saving challenge
    createSavingChallenge($conn, $userID, $lvl);

    echo json_encode([
        "status" => "completed",
        "expAwarded" => $rewardEXP,
        "leveledUp" => $leveledUp,
        "newLevel" => $lvl
    ]);
    exit();
}

// ---------------------------
// 5. NORMAL PROGRESS UPDATE
// ---------------------------
updateSavingDaily10Peso($userID, $conn);
updateSavingWeeklyRow($userID, $conn);

echo json_encode(["status" => "ok"]);
exit();
?>