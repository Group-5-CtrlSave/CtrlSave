<?php

// EXPENSE CHALLENGES
function updateExpenseChallenges($userID, $conn) {

    // DAILY: Add 1 expense today
    $dailyID = 2;

    $checkDaily = "
        SELECT expenseID
        FROM tbl_expense
        WHERE userID = $userID
          AND DATE(dateAdded) = CURDATE()
        LIMIT 1
    ";
    $dailyResult = mysqli_query($conn, $checkDaily);

    if ($dailyResult && mysqli_num_rows($dailyResult) > 0) {
        mysqli_query($conn, "
            UPDATE tbl_userchallenges
            SET status = 'completed', completedAt = NOW()
            WHERE userID = $userID
              AND challengeID = $dailyID
              AND status = 'in progress'
        ");
    }

    // WEEKLY: Add 3 expenses
    $weeklyID = 9;

    $checkWeekly = "
        SELECT COUNT(*) AS total
        FROM tbl_expense
        WHERE userID = $userID
          AND YEARWEEK(dateAdded, 1) = YEARWEEK(CURDATE(), 1)
    ";
    $weeklyResult = mysqli_query($conn, $checkWeekly);
    $row = mysqli_fetch_assoc($weeklyResult);

    if ($row['total'] >= 3) {
        mysqli_query($conn, "
            UPDATE tbl_userchallenges
            SET status = 'completed',
                completedAt = NOW()
            WHERE userID = $userID
              AND challengeID = $weeklyID
              AND status = 'in progress'
        ");
    }
}



// INCOME CHALLENGES
function updateIncomeChallenges($userID, $conn) {

    // WEEKLY: Add 1 income
    $weeklyIncomeID = 10;

    $checkWeeklyIncome = "
        SELECT COUNT(*) AS total
        FROM tbl_income
        WHERE userID = $userID
          AND YEARWEEK(dateReceived, 1) = YEARWEEK(CURDATE(), 1)
    ";
    $weeklyIncomeResult = mysqli_query($conn, $checkWeeklyIncome);
    $row = mysqli_fetch_assoc($weeklyIncomeResult);

    if ($row['total'] >= 1) {
        mysqli_query($conn, "
            UPDATE tbl_userchallenges
            SET status = 'completed',
                completedAt = NOW()
            WHERE userID = $userID
              AND challengeID = $weeklyIncomeID
              AND status = 'in progress'
        ");
    }
}



// SAVING STRATEGY CHALLENGES
function updateSavingVideoChallenge($userID, $conn) {
    $challengeID = 3;

    mysqli_query($conn, "
        UPDATE tbl_userchallenges
        SET status = 'completed',
            completedAt = NOW()
        WHERE userID = $userID
          AND challengeID = $challengeID
          AND status = 'in progress'
    ");
}

function updateSavingArticleChallenge($userID, $conn) {
    $challengeID = 8;

    mysqli_query($conn, "
        UPDATE tbl_userchallenges
        SET status = 'completed',
            completedAt = NOW()
        WHERE userID = $userID
          AND challengeID = $challengeID
          AND status = 'in progress'
    ");
}



// SAVING GOAL DAILY
function updateSavingGoalDailyChallenge($userID, $conn) {
    $challengeID = 4;

    mysqli_query($conn, "
        UPDATE tbl_userchallenges
        SET status = 'completed',
            completedAt = NOW()
        WHERE userID = $userID
          AND challengeID = $challengeID
          AND status = 'in progress'
    ");
}



// DAILY SAVING CHALLENGE (Add at least 10 pesos today)
function updateSavingDaily10Peso($userID, $conn) {
    $challengeID = 5;

    // Sum amount saved today
    $sql = "
        SELECT SUM(amount) AS totalSaved
        FROM tbl_savingchallenge_progress
        WHERE userID = $userID
          AND DATE(dateAdded) = CURDATE()
    ";

    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    $savedToday = intval($row['totalSaved']);

    if ($savedToday >= 10) {
        mysqli_query($conn, "
            UPDATE tbl_userchallenges
            SET status = 'completed', completedAt = NOW()
            WHERE userID = $userID
              AND challengeID = $challengeID
              AND status = 'in progress'
        ");
    }
}



// WEEKLY SAVING CHALLENGE — Complete ANY full row of 5 slots
function updateSavingWeeklyRow($userID, $conn) {
    $challengeID = 7;

    // Load user challenge to access slotData
    $q = mysqli_query($conn, "
        SELECT slotData
        FROM tbl_usersavingchallenge
        WHERE userID = $userID AND status='active'
        LIMIT 1
    ");
    if (!$q || mysqli_num_rows($q) === 0) return;

    $data = mysqli_fetch_assoc($q);
    $slots = json_decode($data['slotData'], true);

    if (!$slots || count($slots) != 20) return;

    // Break into rows of 5
    $rows = array_chunk($slots, 5);

    // Get saved progress for this week
    $savedIndexes = [];

    $res = mysqli_query($conn, "
        SELECT itemIndex
        FROM tbl_savingchallenge_progress
        WHERE userID = $userID
          AND YEARWEEK(dateAdded,1) = YEARWEEK(CURDATE(),1)
    ");

    while ($row = mysqli_fetch_assoc($res)) {
        $savedIndexes[] = intval($row['itemIndex']);
    }

    // Check each row for full completion
    foreach ($rows as $rowIndex => $rowSlotValues) {

        $completed = true;

        for ($i = 0; $i < 5; $i++) {
            $slotIndex = $rowIndex * 5 + $i;

            if (!in_array($slotIndex, $savedIndexes)) {
                $completed = false;
                break;
            }
        }

        if ($completed) {
            // Update challenge
            mysqli_query($conn, "
                UPDATE tbl_userchallenges
                SET status = 'completed', completedAt = NOW()
                WHERE userID = $userID
                  AND challengeID = $challengeID
                  AND status = 'in progress'
            ");
            return;
        }
    }
}

?>
