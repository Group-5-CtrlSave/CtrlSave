<?php

// DAILY CHALLENGES
// Mark Daily challenge as failed if 24 hours passed
function expireDailyChallenges($conn, $userID)
{
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
    $failed = mysqli_query($conn, "
        SELECT u.challengeID
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID
          AND c.type = 'Daily'
          AND u.status = 'failed'
    ");

    if (mysqli_num_rows($failed) == 0) return;

    // delete failed
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID
          AND c.type = 'Daily'
          AND u.status = 'failed'
    ");

    // reassign new identical daily challenges
    while ($row = mysqli_fetch_assoc($failed)) {
        $cid = intval($row['challengeID']);
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, $cid, NOW(), 'in progress')
        ");
    }
}

// Daily Reset Trigger (Only after 24 hours)
function resetDailyChallengeSetIfCompleted($conn, $userID)
{
    $result = mysqli_query($conn, "
        SELECT MIN(u.assignedDate) AS oldest
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Daily'
    ");

    $row = mysqli_fetch_assoc($result);
    if (!$row || !$row['oldest']) return;

    $hoursPassed = (time() - strtotime($row['oldest'])) / 3600;

    if ($hoursPassed < 24) return;

    // reset full daily set
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Daily'
    ");

    // assign fresh daily set
    $daily = mysqli_query($conn, "SELECT challengeID FROM tbl_challenges WHERE type = 'Daily'");
    while ($d = mysqli_fetch_assoc($daily)) {
        $cid = intval($d['challengeID']);
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, $cid, NOW(), 'in progress')
        ");
    }
}



// WEEKLY CHALLENGES
// Mark Weekly challenge as failed if 168 hours passed
function expireWeeklyChallenges($conn, $userID)
{
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


// ❌ Weekly reassign removed — weekly challenges must NOT be reassigned individually



// Weekly Reset Trigger (After full 168 hours)
function resetWeeklyChallengeSetIfCompleted($conn, $userID)
{
    $result = mysqli_query($conn, "
        SELECT MIN(u.assignedDate) AS oldest
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Weekly'
    ");

    $row = mysqli_fetch_assoc($result);
    if (!$row || !$row['oldest']) return;

    $hoursPassed = (time() - strtotime($row['oldest'])) / 3600;

    // only reset after full 168 hours
    if ($hoursPassed < 168) return;

    // delete entire weekly challenge set
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Weekly'
    ");

    // assign a fresh weekly set
    $weekly = mysqli_query($conn, "SELECT challengeID FROM tbl_challenges WHERE type = 'Weekly'");
    while ($d = mysqli_fetch_assoc($weekly)) {
        $cid = intval($d['challengeID']);
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, $cid, NOW(), 'in progress')
        ");
    }
}

?>
