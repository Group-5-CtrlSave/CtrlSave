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

    // insert fresh
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
    // Get the oldest assignedDate among all DAILY challenges
    $result = mysqli_query($conn, "
        SELECT MIN(u.assignedDate) AS oldest
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Daily'
    ");

    $row = mysqli_fetch_assoc($result);

    // If no rows yet, do nothing
    if (!$row || !$row['oldest']) return;

    // Calculate hours passed since assignment
    $hoursPassed = (strtotime("now") - strtotime($row['oldest'])) / 3600;

    // Do NOT reset unless full 24 hours passed
    if ($hoursPassed < 24) return;

    // 24 hours reached → RESET full daily set
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Daily'
    ");

    // Assign fresh daily set
    $daily = mysqli_query($conn, "
        SELECT challengeID FROM tbl_challenges WHERE type = 'Daily'
    ");

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

// Reassign failed weekly
function reassignFailedWeeklyChallenges($conn, $userID)
{
    $failed = mysqli_query($conn, "
        SELECT u.challengeID
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID
          AND c.type = 'Weekly'
          AND u.status = 'failed'
    ");

    if (mysqli_num_rows($failed) == 0) return;

    // delete failed
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID
          AND c.type = 'Weekly'
          AND u.status = 'failed'
    ");

    // reinsert
    while ($row = mysqli_fetch_assoc($failed)) {
        $cid = intval($row['challengeID']);
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, $cid, NOW(), 'in progress')
        ");
    }
}

//  Weekly Reset Trigger (Only after 168 hours)
function resetWeeklyChallengeSetIfCompleted($conn, $userID)
{
    // oldest assigned weekly task
    $result = mysqli_query($conn, "
        SELECT MIN(u.assignedDate) AS oldest
        FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Weekly'
    ");

    $row = mysqli_fetch_assoc($result);
    if (!$row || !$row['oldest']) return;

    $hoursPassed = (strtotime("now") - strtotime($row['oldest'])) / 3600;

    // Not yet 7 days (168 hours)
    if ($hoursPassed < 168) return;

    // Reset entire weekly set
    mysqli_query($conn, "
        DELETE u FROM tbl_userchallenges u
        JOIN tbl_challenges c ON u.challengeID = c.challengeID
        WHERE u.userID = $userID AND c.type = 'Weekly'
    ");

    $weekly = mysqli_query($conn, "
        SELECT challengeID FROM tbl_challenges WHERE type = 'Weekly'
    ");

    while ($d = mysqli_fetch_assoc($weekly)) {
        $cid = intval($d['challengeID']);
        mysqli_query($conn, "
            INSERT INTO tbl_userchallenges (userID, challengeID, assignedDate, status)
            VALUES ($userID, $cid, NOW(), 'in progress')
        ");
    }
}

?>
