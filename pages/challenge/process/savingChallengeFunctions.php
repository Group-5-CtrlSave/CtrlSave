<?php

/* ============================================================
   GET USER LEVEL — auto-create row if missing
   ============================================================ */
function getUserLevel($conn, $userID) {
    $q = mysqli_query($conn, "SELECT lvl FROM tbl_userlvl WHERE userID = $userID");

    if ($row = mysqli_fetch_assoc($q)) {
        return intval($row['lvl']);
    }

    // Auto-create a new row for brand new user
    mysqli_query($conn, "
        INSERT INTO tbl_userlvl (userID, lvl, exp)
        VALUES ($userID, 1, 0)
    ");

    return 1;
}

/* ============================================================
   CREATE NEW SAVING CHALLENGE — closes previous active one
   ============================================================ */
function createSavingChallenge($conn, $userID, $level) {

    $base = 100;         // Level 1 target
    $increment = 50;     // +50 per level
    $target = $base + (($level - 1) * $increment);

    // XP reward = 3 × target
    $expReward = $target * 3;

    // Close any active challenge
    mysqli_query($conn, "
        UPDATE tbl_usersavingchallenge
        SET status = 'completed'
        WHERE userID = $userID AND status = 'active'
    ");

    // Create new active challenge
    mysqli_query($conn, "
        INSERT INTO tbl_usersavingchallenge 
            (userID, level, targetAmount, expReward, status, currentAmount)
        VALUES ($userID, $level, $target, $expReward, 'active', 0)
    ");
}

/* ============================================================
   GET ACTIVE SAVING CHALLENGE
   ============================================================ */
function getActiveSavingChallenge($conn, $userID) {

    $q = mysqli_query($conn, "
        SELECT * FROM tbl_usersavingchallenge
        WHERE userID = $userID AND status = 'active'
        LIMIT 1
    ");

    if (!$q) return null;
    return mysqli_fetch_assoc($q);
}

/* ============================================================
   GENERATE 20 SAVING SLOTS (balanced, always totals target)
   ============================================================ */
function generateSavingSlots($targetAmount) {

    $slots = [];
    $avg = $targetAmount / 20;

    // Generate rough values +/-50% of average
    for ($i = 0; $i < 20; $i++) {

        $min = max(1, floor($avg * 0.5));
        $max = max($min + 1, floor($avg * 1.5));

        $value = rand($min, $max);

        $slots[] = $value;
    }

    // Fix total difference EXACTLY
    $sum = array_sum($slots);
    $diff = $targetAmount - $sum;

    // If too low → add difference to a random slot
    if ($diff > 0) {
        $idx = rand(0, 19);
        $slots[$idx] += $diff;
    }

    // If too high → subtract difference safely
    elseif ($diff < 0) {
        $diff = abs($diff);

        while ($diff > 0) {
            $idx = rand(0, 19);

            if ($slots[$idx] > 1) {
                $slots[$idx]--;
                $diff--;
            }
        }
    }

    return $slots;
}

?>
