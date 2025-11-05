<?php
session_start();
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['userID'])) {
    header(header: "Location: ../../login.php");
    exit;
}

$userID = $_SESSION['userID'];

// query for notif
$query = "
    SELECT notificationTitle, message, icon, createdAt, isRead
    FROM tbl_notifications
    WHERE userID = '$userID'
    ORDER BY createdAt DESC
";

$result = executeQuery($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/notification.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap');

        body {
            font-family: "Roboto", sans-serif;
            background-color: #44B87D !important;
            color: #000000;
        }

        .mainHeader {
            position: sticky;
            top: 0;
            background-color: #44B87D;
            padding: 20px 30px;
            color: #fff;
            font-family: "Poppins", sans-serif;
        }

        .mainHeader h2 {
            font-weight: 700;
        }

        .scrollableContainer {
            height: 77dvh;
            overflow-y: auto;
            padding: 0 20px;
        }

        .notificationCard {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 10px;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .notificationCard:hover {
            background-color: #F0F1F6;
        }

        .notificationCard img {
            width: 50px;
            height: 50px;
            \ 
        }

        .notificationContent p {
            margin: 0;
        }

        .notificationContent .title {
            font-weight: 700;
            font-size: 16px;
        }

        .notificationContent .subtitle {
            font-size: 16px;
        }

        .notificationTime {
            position: absolute;
            bottom: 3px;
            right: 16px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <?php include("../../assets/shared/navigationBar.php") ?>
    <?php include("../../assets/shared/sideBar.php") ?>

    <div class="mainHeader">
        <h2>Notifications</h2>
    </div>

    <div class="scrollableContainer">


        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $formattedTime = date("M d, Y | h:i A", strtotime($row['createdAt']));
                $readClass = $row['isRead'] ? "read" : "";

                $iconFile = ($row['icon']);
                $iconPath = "../../assets/img/";

                //all possible paths
                $psblePaths = [
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
                //if exists 
                foreach ($psblePaths as $path) {
                    if (file_exists($iconPath . $path . $iconFile)) {
                        $iconPath = $iconPath . $path . $iconFile;
                        break;
                    }
                }
                //if not
                if (!file_exists($iconPath)) {
                    $iconPath = "../../assets/img/shared/logo_s.png";
                }

                //for identification color (ALERT)
                //base is alert.png
                $isAlert = (strtolower(pathinfo($iconFile, PATHINFO_FILENAME)) === "alert");
                $titleColor = $isAlert ? "#E63946" : "#44B87D";
                ?>

                <a href="viewNotification.php" style="text-decoration: none; color: inherit;">
                    <div class="notificationCard <?= $readClass ?>">
                        <img src="<?= $iconPath ?>" alt="icon">
                        <div class="notificationContent">
                            <div class="title" style="color: <?= $titleColor ?>;">
                                <?= ($row['notificationTitle']) ?>
                            </div>
                            <div class="subtitle"><?= nl2br(($row['message'])) ?></div>
                        </div>
                        <div class="notificationTime"><?= $formattedTime ?></div>
                    </div>
                </a>

                <?php
            }
        } else {
            echo "<p style='font-size:16px; color:white; text-align:center; margin-top:40px;'>No notifications yet.</p>";
        }
        ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>