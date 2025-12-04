<?php

// EXPENSE CHALLENGES
function updateExpenseChallenges($userID, $conn) {

    // DAILY EXPENSE CHALLENGE
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

    // WEEKLY EXPENSE CHALLENGE 
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
            SET status = 'completed', completedAt = NOW()
            WHERE userID = $userID
              AND challengeID = $weeklyID
              AND status = 'in progress'
        ");
    }
}

// INCOME CHALLENGES
function updateIncomeChallenges($userID, $conn) {

    // WEEKLY INCOME CHALLENGE 
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

// SAVING STRATEGY
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

// SAVING Goal

function updateSavingGoalDailyChallenge($userID, $conn) {
    $challengeID = 4; // Daily: Add 1 saving goal

    $sql = "
        UPDATE tbl_userchallenges
        SET status = 'completed',
            completedAt = NOW()
        WHERE userID = $userID
          AND challengeID = $challengeID
          AND status = 'in progress'
    ";

    mysqli_query($conn, $sql);
}

// SAVING Challenge
function updateSavingDaily10Peso($userID, $conn) {
    $challengeID = 5;

    $sql = "
        SELECT id 
        FROM tbl_savingchallenge_progress
        WHERE userID = $userID
          AND amount = 10
          AND DATE(dateAdded) = CURDATE()
        LIMIT 1
    ";

    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        mysqli_query($conn, "
            UPDATE tbl_userchallenges
            SET status = 'completed', completedAt = NOW()
            WHERE userID = $userID
              AND challengeID = $challengeID
              AND status = 'in progress'
        ");
    }
}



function updateSavingWeeklyRow($userID, $conn) {
    $challengeID = 7;

    // Use itemIndex (0–9) NOT amount
    $rows = [
        [0, 1, 2, 3, 4], // Row 1
        [5, 6, 7, 8, 9]  // Row 2
    ];

    foreach ($rows as $row) {
        $count = 0;

        foreach ($row as $idx) {
            $query = "
                SELECT id
                FROM tbl_savingchallenge_progress
                WHERE userID = $userID
                  AND itemIndex = $idx
                  AND YEARWEEK(dateAdded,1) = YEARWEEK(CURDATE(),1)
                LIMIT 1
            ";

            $res = mysqli_query($conn, $query);
            if ($res && mysqli_num_rows($res) > 0) {
                $count++;
            }
        }

        // If full row (5 items) completed → complete the challenge
        if ($count === 5) {
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
