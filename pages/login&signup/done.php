<?php
session_start();
include("../../assets/shared/connect.php");

// Make sure user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}

$userID = (int)$_SESSION['userID'];

if (isset($_POST['btnDone'])) {

    // INSERT LOGIN HISTORY
    $today = date('Y-m-d');

    $checkLogin = "
        SELECT loginID
        FROM tbl_loginhistory
        WHERE userID = $userID
          AND DATE(loginDate) = '$today'
        LIMIT 1
    ";
    $resultLogin = $conn->query($checkLogin);

    if ($resultLogin && $resultLogin->num_rows === 0) {
        $insertLogin = "
            INSERT INTO tbl_loginhistory (userID, loginDate)
            VALUES ($userID, NOW())
        ";
        $conn->query($insertLogin);
    }

  
    $dailyLoginChallengeID = 1; 

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
        $ucID = (int)$rowDaily['userChallengeID'];

        $updateDaily = "
            UPDATE tbl_userchallenges
            SET status = 'completed',
                completedAt = NOW()
            WHERE userChallengeID = $ucID
        ";
        $conn->query($updateDaily);
    }

    $weeklyLoginChallengeID = 6;

    $countLogin = "
        SELECT COUNT(DISTINCT DATE(loginDate)) AS daysLogged
        FROM tbl_loginhistory
        WHERE userID = $userID
          AND YEARWEEK(loginDate) = YEARWEEK(NOW())
    ";
    $countResult = $conn->query($countLogin);
    if ($countResult) {
        $countRow   = $countResult->fetch_assoc();
        $daysLogged = (int)$countRow['daysLogged'];

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
                $ucIDweek = (int)$rowWeekly['userChallengeID'];

                $updateWeekly = "
                    UPDATE tbl_userchallenges
                    SET status = 'completed',
                        completedAt = NOW()
                    WHERE userChallengeID = $ucIDweek
                ";
                $conn->query($updateWeekly);
            }
        }
    }

    // Redirect to home after everything
    header("Location: ../home/home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* ADS */
        .wave {
            position: absolute;
            width: 100%;
            height: 90%;
            background: url('../../assets/img/login&signup/waveDown.png') no-repeat center;
            background-size: cover;
        }

        .content {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 90vh;
            text-align: center;
            z-index: 1;
        }

        .woman-img {
            width: 100%;
            max-width: 500px;
            margin-bottom: 50px;
            margin-top: -90px;
            text-align: center;
        }

        .woman-img img {
            width: 250px;
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .step-text {
            font-size: 60px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #ffffff;
            margin-top: -40px;
            font-family: "Poppins", sans-serif;
            font-size: 20px;
        }

        .description {
            font-family: "Roboto", sans-serif;
            font-size: 16px;
            color: #ffffff;
            margin-bottom: 5px;
            max-width: 80%;
            margin-bottom: 30px;
            margin-top: -30px;
        }

        /* Button */
        .btn {
            background-color: #F6D25B;
            color: black;
            text-align: center;
            width: 125px;
            font-size: 20px;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 27px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            margin-top: 10px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

    </style>
</head>

<body>
    <div class="wave"></div>
    <div class="content">
        <div class="woman-img">
            <img src="../../assets/img/login&signup/allset.png" alt="allset">
        </div>
        <div class="step-text">You're all set!</div>
        <div class="description">You can now start your saving journey.</div>
    </div>
    <div class="col-12 d-flex justify-content-center">
        <form method="POST">
            <button type="submit" class="btn btn-warning mb-3" name="btnDone">Next</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
