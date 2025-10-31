<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Bill Due</title>
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

        .categoryImage {
            height: 100px;
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
            height: 70dvh;
            padding: 25px;
            background-color: #fff;
            border-radius: 20px;
            width: 90%;
            margin: 5px auto;
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
            margin-bottom: 5px;
        }

        .notifSubtitle {
            font-size: 16px;
            color: #000;
        }

        .notifDetails {
            margin-top: 20px;
            font-size: 16px;
            color: #000;
        }

        .notifTime {
            text-align: right;
            color: #666;
            font-size: 12px;
            margin-top: 25px;
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
    </nav>

    <div class="mainHeader">
        <h2>Notification Detail</h2>
    </div>

    <div class="scrollableContainer">
        <div class="notifHeader">
            <img src="../../assets/img/shared/categories/expense/Electricity.png" alt="Electricity Icon">
            <div>
                <p class="notifTitle">Electricity Bill Due</p>
                <p class="notifSubtitle">Your electricity bill payment is approaching the due date.</p>
            </div>
        </div>

        <div class="notifDetails">
            <p><strong>Due Date:</strong> June 07, 2025</p>
            <p>
                Make sure to pay your electricity bill before the due date to avoid penalties or service interruptions.
                You can pay through your preferred payment channels or directly at your electric company’s office.
            </p>
            <p>
                Stay on top of your bills to maintain good credit and avoid unexpected disconnections.
            </p>
        </div>

        <div class="notifTime">08:00 AM | June 07, 2025</div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>