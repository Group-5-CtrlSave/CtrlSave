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

        .notificationCard img.icon {
            width: 45px;
            height: 45px;
            object-fit: contain;
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
            bottom: 6px;
            right: 16px;
            font-size: 12px;
            color: #666;
        }

        /* Different types of notifications */
        .notifExpense .title {
            color: #44B87D;
        }

        .notifAlert .title {
            color: #E63946;
        }

        .notifSavings .title {
            color: #44B87D;
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

        <a href="viewNotification.php" style="text-decoration: none; color: inherit;">
            <div class="notificationCard notifExpense">
                <img src="../../assets/img/shared/categories/expense/Electricity.png" alt="Electricity" class="icon">
                <div class="notificationContent">
                    <p class="title">Electricity</p>
                    <p class="subtitle">
                        <span style="color: #F6D25B; font-weight: 500;">Due Date:</span> June 07, 2025
                    </p>
                </div>
                <div class="notificationTime">08:00 AM</div>
            </div>
        </a>

        <div class="notificationCard notifAlert">
            <img src="../../assets/img/notification/alert.png" alt="Alert" class="icon">
            <div class="notificationContent">
                <p class="title">Transportation Limit Exceeded</p>
                <p class="subtitle">
                    <span style="color: #F6D25B; font-weight: 500;">Limit:</span> 15% (₱1,500)<br>
                    <span style="color: #44B87D; font-weight: 500;">Spent:</span> ₱2,000
                </p>
            </div>
            <div class="notificationTime">May 20, 2025 | 10:30 AM</div>
        </div>

        <div class="notificationCard notifSavings">
            <img src="../../assets/img/shared/categories/Savings.png" alt="Savings" class="icon"
                style="border-radius: 50%;">
            <div class="notificationContent">
                <p class="title">Saving Goals</p>
                <p class="subtitle">Set and monitor goals — vacation, gadgets, or emergency fund.</p>
            </div>
            <div class="notificationTime">May 14, 2025 | 11:16 AM</div>
        </div>

        <div class="notificationCard notifExpense">
            <img src="../../assets/img/shared/categories/expense/Electricity.png" alt="Electricity" class="icon">
            <div class="notificationContent">
                <p class="title">Electricity</p>
                <p class="subtitle"><span style="color: #F6D25B; font-weight: 500;">Due Date:</span> June 07, 2025</p>
            </div>
            <div class="notificationTime">08:00 AM</div>
        </div>

        <div class="notificationCard notifAlert">
            <img src="../../assets/img/notification/alert.png" alt="Alert" class="icon">
            <div class="notificationContent">
                <p class="title">Transportation Limit Exceeded</p>
                <p class="subtitle">
                    <span style="color: #F6D25B; font-weight: 500;">Limit:</span> 15% (₱1,500)<br>
                    <span style="color: #44B87D; font-weight: 500;">Spent:</span> ₱2,000
                </p>
            </div>
            <div class="notificationTime">May 20, 2025 | 10:30 AM</div>
        </div>

        <div class="notificationCard notifSavings">
            <img src="../../assets/img/shared/categories/Savings.png" alt="Savings" class="icon"
                style="border-radius: 50%;">
            <div class="notificationContent">
                <p class="title">Saving Goals</p>
                <p class="subtitle">Set and monitor goals — vacation, gadgets, or emergency fund.</p>
            </div>
            <div class="notificationTime">May 14, 2025 | 11:16 AM</div>
        </div>

        <div class="notificationCard notifExpense">
            <img src="../../assets/img/shared/categories/expense/Electricity.png" alt="Electricity" class="icon">
            <div class="notificationContent">
                <p class="title">Electricity</p>
                <p class="subtitle"><span style="color: #F6D25B; font-weight: 500;">Due Date:</span> June 07, 2025</p>
            </div>
            <div class="notificationTime">08:00 AM</div>
        </div>

        <div class="notificationCard notifAlert">
            <img src="../../assets/img/notification/alert.png" alt="Alert" class="icon">
            <div class="notificationContent">
                <p class="title">Transportation Limit Exceeded</p>
                <p class="subtitle">
                    <span style="color: #F6D25B; font-weight: 500;">Limit:</span> 15% (₱1,500)<br>
                    <span style="color: #44B87D; font-weight: 500;">Spent:</span> ₱2,000
                </p>
            </div>
            <div class="notificationTime">May 20, 2025 | 10:30 AM</div>
        </div>

        <div class="notificationCard notifSavings">
            <img src="../../assets/img/shared/categories/Savings.png" alt="Savings" class="icon"
                style="border-radius: 50%;">
            <div class="notificationContent">
                <p class="title">Saving Goals</p>
                <p class="subtitle">Set and monitor goals — vacation, gadgets, or emergency fund.</p>
            </div>
            <div class="notificationTime">May 14, 2025 | 11:16 AM</div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>