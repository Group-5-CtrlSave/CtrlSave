<?php

// ===============================
// Set timezone function (SAFE)
// ===============================
function setTimezone($conn) {
    if ($conn) {
        $conn->query("SET time_zone = '+08:00'");
    }
}

// =====================================================
// DAILY CHALLENGES
// =====================================================

// Mark Daily challenge as failed if 24 hours passed
function expireDailyChallenges($conn, $userID)
{
    setTimezone($conn);

    mysqli_query($conn, "
        UPDATE tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        SET u.status = 'failed'
        WHERE u.userID = $userID
          AND c.type = 'Daily'
          AND u.status = 'in progress'
          AND TIMESTAMPDIFF(HOUR, u.assignedDate, NOW()) >= 24
    ");
}

// Reassign only failed Daily challenges
function reassignFailedDailyChallenges($conn, $userID)
{
    setTimezone($conn);

    $failed = mysqli_query($conn, "
        SELECT u.challengeID
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID
          AND c.type = 'Daily'
          AND u.status = 'failed'
    ");

    if (mysqli_num_rows($failed) == 0) return;

    // Delete failed
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID
          AND c.type = 'Daily'
          AND u.status = 'failed'
    ");

    // Reassign same challenges
    while ($row = mysqli_fetch_assoc($failed)) {
        $cid = intval($row['challengeID']);
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, $cid, NOW(), 'in progress')
        ");
    }
}

// Daily Reset Trigger (After 24 hours)
function resetDailyChallengeSetIfCompleted($conn, $userID)
{
    setTimezone($conn);

    // One query handles everything
    $result = mysqli_query($conn, "
        SELECT TIMESTAMPDIFF(HOUR, MIN(u.assignedDate), NOW()) AS hoursPassed
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Daily'
    ");

    $row = mysqli_fetch_assoc($result);

    if (!$row || $row['hoursPassed'] < 24) return;

    // Reset full daily set
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Daily'
    ");

    // Assign new daily set
    $daily = mysqli_query($conn, "SELECT challengeID FROM tbl_challenges WHERE type = 'Daily'");
    while ($d = mysqli_fetch_assoc($daily)) {
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, {$d['challengeID']}, NOW(), 'in progress')
        ");
    }
}


// =====================================================
// WEEKLY CHALLENGES
// =====================================================

// Mark Weekly challenge as failed if 168 hours passed
function expireWeeklyChallenges($conn, $userID)
{
    setTimezone($conn);

    mysqli_query($conn, "
        UPDATE tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        SET u.status = 'failed'
        WHERE u.userID = $userID
          AND c.type = 'Weekly'
          AND u.status = 'in progress'
          AND TIMESTAMPDIFF(HOUR, u.assignedDate, NOW()) >= 168
    ");
}


// Weekly Reset Trigger (After 168 hours)
function resetWeeklyChallengeSetIfCompleted($conn, $userID)
{
    setTimezone($conn);

    // One query handles everything
    $result = mysqli_query($conn, "
        SELECT TIMESTAMPDIFF(HOUR, MIN(u.assignedDate), NOW()) AS hoursPassed
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Weekly'
    ");

    $row = mysqli_fetch_assoc($result);
    if (!$row || $row['hoursPassed'] < 168) return;

    // Reset weekly set
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Weekly'
    ");

    // Assign new weekly set
    $weekly = mysqli_query($conn, "SELECT challengeID FROM tbl_challenges WHERE type = 'Weekly'");
    while ($w = mysqli_fetch_assoc($weekly)) {
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, {$w['challengeID']}, NOW(), 'in progress')
        ");
    }
}

?>
