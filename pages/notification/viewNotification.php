<?php
session_start();
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');


$userID = 0;
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
} else {
    $userID = $_SESSION['userID'];
}


$notificationID = intval($_GET['id']);


if (isset($_POST['btnPaid'])) {

    $recurringID = intval($_POST['recurringID']);

    $getRecurringTransactionQuery = "SELECT `userCategoryID`, `amount`, `note`, `frequency`, `userBudgetVersionID` 
    FROM `tbl_recurringtransactions` 
    WHERE userID = $userID 
    AND recurringID = $recurringID LIMIT 1";
    $recurringTransactionResult = executeQuery($getRecurringTransactionQuery);

    if (mysqli_num_rows($recurringTransactionResult) > 0) {
        $recurringrow = mysqli_fetch_assoc($recurringTransactionResult);
        $userCategoryID = intval($recurringrow['userCategoryID']);
        $amount = floatval($recurringrow['amount']);
        $note = $recurringrow['note'];
        $frequency = $recurringrow['frequency'];
        $userBudgetVersionID = $recurringrow['userBudgetVersionID'];

        $nextDueDate = '';

        switch ($frequency) {
            case 'daily':
                $nextDueDate = "NOW() + INTERVAL 1 DAY";
                break;

            case 'weekly':
                $nextDueDate = "NOW() + INTERVAL 1 WEEK";
                break;

            case 'monthly':
                $nextDueDate = "NOW() + INTERVAL 1 MONTH";
                break;

            default:
                $nextDueDate = '';
                break;
        }


        $insertExpenseQuery = "INSERT INTO `tbl_expense`(`userID`, `amount`, `userCategoryID`, `dateSpent`, `dueDate`, `dateAdded`, `isRecurring`, `note`, `recurringID`, `userBudgetversionID`) 
    VALUES ($userID,$amount,'$userCategoryID',NOW(),NULL,NOW(),1,'$note','$recurringID','$userBudgetVersionID')";
        executeQuery($insertExpenseQuery);

        $updateRecurringTransactionQuery = "UPDATE `tbl_recurringtransactions` 
    SET `nextDuedate`= $nextDueDate
    WHERE recurringID = $recurringID";
        executeQuery($updateRecurringTransactionQuery);
        $updateNotificationQuery = "UPDATE `tbl_notifications` 
    SET `isPaid`= 1 
    WHERE recurringID = $recurringID 
    AND notificationID = $notificationID";
        executeQuery($updateNotificationQuery);

        header("Location: viewNotification.php?id=" . $notificationID);
        $_SESSION['successtag'] = "Expense Successfully Added!";
        exit;
    }

}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:white; text-align:center;'>Invalid notification.</p>");
}



//query for showing details in each notif
$query = "
    SELECT notificationTitle, message, icon, createdAt, type, recurringID, isPaid
    FROM tbl_notifications
    WHERE notificationID = $notificationID
    LIMIT 1
";

$result = executeQuery($query);

if ($result->num_rows == 0) {
    die("<p style='color:white; text-align:center;'>Notification not found.</p>");
}

$row = $result->fetch_assoc();

$formattedTime = date("F d, Y | h:i A ", strtotime($row['createdAt']));

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

//if exists
foreach ($paths as $p) {
    if (file_exists($iconPath . $p . $iconFile)) {
        $iconPath = $iconPath . $p . $iconFile;
        break;
    }
}

//if not
if (!file_exists($iconPath)) {
    $iconPath = "../../assets/img/shared/logo_M.png";
}

// if alert. it should be shade of red
$isAlert = (strtolower(pathinfo($iconFile, PATHINFO_FILENAME)) === "alert");
$titleColor = $isAlert ? "#E63946" : "#44B87D";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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

        .paidButton {
            border-radius: 20px !important;
            background-color: #F6D25B !important;
            min-width: 250px;

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

    </nav>

    <?php include("../../assets/shared/successtag.php"); ?>

    <!-- contents (full after ...) -->
    <div class="scrollableContainer">
        <img src="<?= $iconPath ?>" alt="Icon" class="notifIcon">

        <p class="notifTitle" style="color: <?= $titleColor ?>;">
            <?= $row['notificationTitle'] ?>
        </p>

        <p class="notifMessage">
            <?= nl2br($row['message']) ?>
        </p>

        <?php if ($row['type'] === 'recurring' && $row['isPaid'] == 0) { ?>
            <form method="POST">
                <div class="container py-2 text-center">
                    <button class="btn btn-lg paidButton" type="submit" name="btnPaid"><b>Paid</b></button>
                    <input type="hidden" value="<?php echo $row['recurringID'] ?>" name="recurringID">
                </div>
            </form>
        <?php } ?>


        <div class="notifTime"><?= $formattedTime ?></div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function () {
            var alertElement = document.getElementById('myAlert');
            var alert = new bootstrap.Alert(alertElement);
            alert.close();
        }, 2000); 
    </script>

</body>

</html>