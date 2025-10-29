<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/notification.css">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body style="background-color: #44B87D;">

    <?php include ("../../assets/shared/navigationBar.php") ?>
    <?php include ("../../assets/shared/sideBar.php")?>

    <!-- Main Content -->
    <div class="min-vh-100 p-3">
        <h2 class="fs-4 fw-bold mb-3 text-white">Notifications</h2>

        <!-- Notification 1 -->
        <div style="background-color: #F0F1F6; border-radius: 15px; padding: 16px; margin-bottom: 16px; position: relative;">
            <div style="display: flex; align-items: center;">
                <img src="..\..\assets\img\shared\categories\expense\Electricity.png" alt="Electricity" style="width: 40px; height: 40px; margin-right: 12px;">
                <div>
                    <p style="margin: 0; color: #44B87D; font-weight: bold;">Electricity</p>
                    <p style="margin: 0; font-size: 14px;"><span style="color: #F6D25B; font-weight: 500;">Due Date:</span> June 07, 2025</p>
                </div>
            </div>
            <div style="position: absolute; bottom: 0px; right: 16px; font-size: 12px; color: #9FB4A4;">08:00AM</div>
        </div>

        <!-- Notification 2 -->
        <div style="background-color: #F0F1F6; border-radius: 15px; padding: 16px; margin-bottom: 16px; position: relative;">
            <div style="display: flex; align-items: center;">
                <img src="..\..\assets\img\notification\alert.png" alt="Alert" style="width: 40px; height: 40px; margin-right: 12px;">
                <div>
                    <p style="margin: 0; color: #E63946; font-weight: bold;">Transportation Limit Exceeded</p>
                    <p style="margin: 0; font-size: 14px;">
                        <span style="color: #F6D25B; font-weight: 500;">Limit:</span> 15% (1,500)<br />
                        <span style="color: #44B87D; font-weight: 500;">Spent:</span> 2,000
                    </p>
                </div>
            </div>
            <div style="position: absolute; bottom: 0px; right: 16px; font-size: 12px; color: #9FB4A4;">May 20, 2025 | 10:30AM</div>
        </div>

        <!-- Notification 3 -->
        <div style="background-color: #F0F1F6; border-radius: 15px; padding: 16px; margin-bottom: 16px; position: relative;">
            <div style="display: flex; align-items: center;">
                <img src="..\..\assets\img\shared\categories\Savings.png" alt="Savings" style="width: 40px; height: 40px; margin-right: 12px; border-radius: 50%;">
                <div>
                    <p style="margin: 0; color: #44B87D; font-weight: bold;">Saving Goals</p>
                    <p style="margin: 0; font-size: 14px;">Set and monitor goals—be it a vacation, gadget, or emergency fund</p>
                </div>
            </div>
            <div style="position: absolute; bottom: 0px; right: 16px; font-size: 12px; color: #9FB4A4;">May 14, 2025 | 11:16AM</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
