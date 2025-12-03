<?php

function updateExpenseChallenges($userID, $conn) {

    // DAILY CHALLENGE ID
    $dailyID = 2;

    // Did the user add ANY expense today?
    $checkDaily = "
        SELECT expenseID
        FROM tbl_expense
        WHERE userID = $userID
          AND DATE(dateAdded) = CURDATE()
        LIMIT 1
    ";

    $dailyResult = mysqli_query($conn, $checkDaily);

    if ($dailyResult && mysqli_num_rows($dailyResult) > 0) {

        // Mark daily challenge as completed
        mysqli_query($conn, "
            UPDATE tbl_userchallenges
            SET status = 'completed', completedAt = NOW()
            WHERE userID = $userID 
              AND challengeID = $dailyID
              AND status = 'in progress'
        ");
    }

    // WEEKLY CHALLENGE ID
    $weeklyID = 9;

    // Count expenses added this week
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

function updateIncomeChallenges($userID, $conn) {

    // WEEKLY INCOME CHALLENGE ID
    $weeklyIncomeID = 10;

    // Count incomes added THIS WEEK
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


function updateSavingVideoChallenge($userID, $conn) {
    $challengeID = 3; // Daily: Watch a saving strategy video

    $query = "
        UPDATE tbl_userchallenges
        SET status = 'completed',
            completedAt = NOW()
        WHERE userID = $userID
          AND challengeID = $challengeID
          AND status = 'in progress'
    ";

    mysqli_query($conn, $query);
}