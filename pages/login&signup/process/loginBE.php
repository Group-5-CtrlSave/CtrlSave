<?php
session_start();
include("../../assets/shared/connect.php");

if (isset($_POST['btnLogin'])) {

    $emailUsername = trim($_POST['emailUsername']);
    $password = trim($_POST['password']);
    // Set MySQL session timezone to Manila
    $conn->query("SET time_zone = '+08:00'");

    // Escape inputs to avoid SQL injection
    $emailUsernameEsc = $conn->real_escape_string($emailUsername);

    // Query user
    $sql = "
        SELECT userID, userName, email, password
        FROM tbl_users
        WHERE BINARY email = '$emailUsernameEsc'
           OR BINARY userName = '$emailUsernameEsc'
        LIMIT 1
    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            // SESSION
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['userName'] = $row['userName'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['currencyCode'] = $row['currencyCode'];


            $userID = $row['userID'];

            // INSERT LOGIN HISTORY (only once per day)
            $today = date('Y-m-d');

            $checkLogin = "SELECT loginID
            FROM tbl_loginhistory
            WHERE userID = $userID
            AND DATE(loginDate) = '$today'
            LIMIT 1";

            $resultLogin = $conn->query($checkLogin);

            if ($resultLogin && $resultLogin->num_rows === 0) {
                $insertLogin = "INSERT INTO tbl_loginhistory (userID, loginDate)
                VALUES ($userID, NOW())";

                $conn->query($insertLogin);
            }

            $dailyLoginChallengeID = 1; // update with your real challengeID

            // Check if assigned & in progress
            $checkDaily = "
                SELECT userChallengeID
                FROM tbl_userchallenges
                WHERE userID = $userID
                AND challengeID = $dailyLoginChallengeID
                AND status = 'in progress'
                LIMIT 1
            ";

            $resultDaily = $conn->query($checkDaily);

            if ($resultDaily && $resultDaily->num_rows > 0) {
                $rowDaily = $resultDaily->fetch_assoc();
                $ucID = $rowDaily['userChallengeID'];

                // Mark as completed
                $updateDaily = "
                    UPDATE tbl_userchallenges
                    SET status = 'completed',
                        completedAt = NOW()
                    WHERE userChallengeID = $ucID
                ";
                $conn->query($updateDaily);
            }

            $weeklyLoginChallengeID = 6;  // update with your real challengeID

            // Count distinct login days this week
            $countLogin = "
                SELECT COUNT(DISTINCT DATE(loginDate)) AS daysLogged
                FROM tbl_loginhistory
                WHERE userID = $userID
                AND YEARWEEK(loginDate) = YEARWEEK(NOW());
            ";
            $countResult = $conn->query($countLogin);
            $countRow = $countResult->fetch_assoc();
            $daysLogged = $countRow['daysLogged'];

            if ($daysLogged >= 5) {

                $checkWeekly = "
                    SELECT userChallengeID
                    FROM tbl_userchallenges
                    WHERE userID = $userID
                    AND challengeID = $weeklyLoginChallengeID
                    AND status = 'in progress'
                    LIMIT 1
                ";
                $resultWeekly = $conn->query($checkWeekly);

                if ($resultWeekly && $resultWeekly->num_rows > 0) {
                    $rowWeekly = $resultWeekly->fetch_assoc();
                    $ucIDweek = $rowWeekly['userChallengeID'];

                    $updateWeekly = "
                        UPDATE tbl_userchallenges
                        SET status = 'completed',
                            completedAt = NOW()
                        WHERE userChallengeID = $ucIDweek
                    ";
                    $conn->query($updateWeekly);
                }
            }

            // Redirect to home
            header("Location: ../home/home.php", true, 302);
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            exit;

        } else {
            $error = "Incorrect Password";
        }

    } else {
        $error = "User not found. Incorrect Email/Username.";
    }
}
?>