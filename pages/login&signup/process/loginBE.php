<?php
session_start();

include("../../assets/shared/connect.php");
if (!isset($_SESSION['userID']) && isset($_COOKIE['remember_me'])) {

    $token = hash('sha256', $_COOKIE['remember_me']);

    $sql = "
        SELECT u.userID, u.userName, u.email
        FROM tbl_usertokens t
        JOIN tbl_users u ON u.userID = t.userID
        WHERE t.token = '$token'
        AND t.expiry > NOW()
        LIMIT 1
    ";

    $result = executeQuery($sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['userID'] = $user['userID'];
        $_SESSION['userName'] = $user['userName'];
        $_SESSION['email'] = $user['email'];
    } else {
        setcookie("remember_me", "", time() - 3600, "/");
    }
}


if (isset($_SESSION['userID'])) {
    header("Location: ../home/home.php");
    exit;
}
if (isset($_POST['btnLogin'])) {

    $emailUsername = trim($_POST['emailUsername']);
    $password = trim($_POST['password']);
    // Set MySQL session timezone to Manila
    $conn->query("SET time_zone = '+08:00'");

    // Escape inputs to avoid SQL injection
    $emailUsernameEsc = $conn->real_escape_string($emailUsername);

    // Query user
    $sql = "
        SELECT userID, userName, email, password, currencyCode
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

            // Generate remember token
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);
            $expiry = date("Y-m-d H:i:s", strtotime("+30 days"));

            //Save token in database
            $saveToken = "INSERT INTO tbl_usertokens (userId, token, expiry) VALUES ($userID, '$hashedToken', '$expiry');";
            $conn -> query($saveToken);

            //Set cookie (30 days)
            setcookie("remember_me", $token, time() + 30*24*60*60, "/", "", false, true);

            // Redirect to home
            header("Location: ../home/home.php");
            exit;

        } else {
            $error = "Incorrect Password";
        }

    } else {
        $error = "User not found. Incorrect Email/Username.";
    }
}
?>