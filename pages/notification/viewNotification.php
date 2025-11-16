<?php
session_start();
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:white; text-align:center;'>Invalid notification.</p>");
}

$notificationID = intval($_GET['id']);

//query for showing details in each notif
$query = "
    SELECT notificationTitle, message, icon, createdAt
    FROM tbl_notifications
    WHERE notificationID = $notificationID
    LIMIT 1
";

$result = executeQuery($query);

if ($result->num_rows == 0) {
    die("<p style='color:white; text-align:center;'>Notification not found.</p>");
}


$row = $result->fetch_assoc();

$formattedTime = date("h:i A | F d, Y", strtotime($row['createdAt']));

$iconFile = $row['icon'];
$iconPath = "../../assets/img/";
$paths = [
    "challenge/",
    "home/",
    "landing&ads/",
    "login&signup/",
    "notification/",
    "savings/",
    "settings/",
    "shared/",
    "shared/categories/expense/",
    "shared/categories/income/",
    "shared/categories/savings/",
    "shared/sidebar/"
];

//if exixts
foreach ($paths as $p) {
    if (file_exists($iconPath . $p . $iconFile)) {
        $iconPath = $iconPath . $p . $iconFile;
        break;
    }
}

//if not
if (!file_exists($iconPath)) {
    $iconPath = "../../assets/img/shared/logo_s.png";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Notification Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap');

        body {
            background-color: #44B87D;
            font-family: "Roboto", sans-serif;
            color: #000;
        }

        .mainHeader {
            position: sticky;
            background-color: #44B87D;
            padding: 20px 30px;
            color: #fff;
            font-family: "Poppins", sans-serif;
        }

        .mainHeader h2 {
            font-weight: 700;
        }

        .scrollableContainer {
            background-color: #fff;
            width: 90%;
            margin: 15px auto;
            border-radius: 20px;
            padding: 35px 25px;
            text-align: center;
            margin-top: 80px;
        }

        .notifIcon {
            height: 110px;
            display: block;
            margin: 0 auto 15px auto;
        }

        .notifHeader {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notifTitle {
            font-size: 20px;
            font-weight: 700;
            color: #44B87D;
            margin-bottom: 10px;
        }

        .notifMessage {
            text-align: left;
            font-size: 16px;
            color: #000;
            line-height: 1.5;
            text-indent: 25px;
            word-break: break-word;
            margin-top: 5px;
            margin-left: 8px;
        }

        .notifTime {
            text-align: right;
            color: #666;
            font-size: 12px;
            margin-top: 25px;
        }

        .navigationBarTitle {
            font-family: "Poppins", sans-serif;
        }
    </style>
</head>

<body>
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="notification.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>
        </div>

        <div class="position-absolute top-50 start-50 translate-middle">
            <h2 class="m-0 text-center navigationBarTitle">Detail</h2>
        </div>

        </div>

    </nav>

    <!-- contents (full after ...) -->
    <div class="scrollableContainer">
        <img src="<?= $iconPath ?>" alt="Icon" class="notifIcon">

        <p class="notifTitle"><?= $row['notificationTitle'] ?></p>

        <p class="notifMessage">
            <?= nl2br($row['message']) ?>
        </p>

        <div class="notifTime"><?= $formattedTime ?></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>