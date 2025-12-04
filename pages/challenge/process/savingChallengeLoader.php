<?php
include("savingChallengeFunctions.php");

// Always ensure $userID exists
$userID = intval($userID);

// Get user's level (auto-create row if not exists)
$userLevel = getUserLevel($conn, $userID);

// Get active saving challenge
$challenge = getActiveSavingChallenge($conn, $userID);

// If no challenge exists → create one
if (!$challenge) {
    createSavingChallenge($conn, $userID, $userLevel);
    $challenge = getActiveSavingChallenge($conn, $userID);
}

$challengeID     = intval($challenge['userSavingChallengeID']);
$targetAmount    = intval($challenge['targetAmount']);
$currentAmount   = intval($challenge['currentAmount']);
$challengeLevel  = intval($challenge['level']);
$expReward       = intval($challenge['expReward']);


// LOAD OR GENERATE SAVING SLOTS (20 values ONLY ONCE)
if (!empty($challenge['slotData'])) {

    // Load previously stored slots
    $savingAmounts = json_decode($challenge['slotData'], true);

} else {

    // Generate slots ONCE
    $savingAmounts = generateSavingSlots($targetAmount);
    $json = json_encode($savingAmounts);

    // Save generated slots to DB
    mysqli_query($conn, "
        UPDATE tbl_usersavingchallenge
        SET slotData = '$json'
        WHERE userSavingChallengeID = $challengeID
    ");
}


// LOAD USER SAVING PROGRESS
$savingProgress = [];

$q = mysqli_query($conn, "
    SELECT itemIndex, amount 
    FROM tbl_savingchallenge_progress
    WHERE userID = $userID
");

if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $savingProgress[intval($row['itemIndex'])] = intval($row['amount']);
    }
}
?>
