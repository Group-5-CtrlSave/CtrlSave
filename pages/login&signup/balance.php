<?php
include("../../pages/login&signup/process/balanceBE.php");

$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Set balance</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter&family=Montserrat&family=Nanum+Myeongjo&family=Roboto&display=swap');

        body {
            background-color: #44B87D;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
            text-align: center;
        }

        .desc {
            font-family: "Roboto", sans-serif;
            font-size: 16px;
            color: #ffff;
            text-align: center;
        }

        .form-control {
            border: 2px solid #F6D25B;
            height: 60px;
            width: 200px;
            text-align: center;
            font-size: 20px;
            background-color: white;
            border-radius: 20px;
            font-family: "Roboto", sans-serif;
        }

        .btn {
            background-color: #F6D25B;
            color: black;
            width: 125px;
            font-size: 20px;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 27px;
            border: none;
            margin-top: 10px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px rgba(0, 0, 0, .24), 0 17px 50px rgba(0, 0, 0, .19);
        }

        /* Error Handling */
        #errorToast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #E63946;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            width: 300px;
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
            animation: fadeInOut 3s ease forwards;
            text-align: center;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translate(-50%, -5px);
            }

            10%,
            70% {
                opacity: 1;
                transform: translate(-50%, 0);
            }

            100% {
                opacity: 0;
                transform: translate(-50%, -5px);
            }
        }

        ::placeholder {
            color: rgba(0, 0, 0, 0.35);
        }
    </style>

</head>

<body>

    <?php if (!empty($error)) { ?>
        <div id="errorToast"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top"
        style="height: 75px;">
        <div class="container-fluid position-relative">
            <div class="position-absolute top-70 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Set Balance</h2>
            </div>
        </div>
    </nav>

    <!-- UI -->
    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-5">
        <div class="row main-row">

            <div class="col-12 title">
                <h2>Set up your cash<br>balance</h2>
            </div>

            <div class="col-12 desc mt-3 mb-4">
                <p>How much cash do you have in<br>your wallet right now?</p>
            </div>

            <form method="POST" id="balanceForm"
                class="col-12 amount mt-5 mb-5 d-flex justify-content-center align-items-center">
                <input type="text" name="balance" id="balanceInput" placeholder="<?= $symbol ?>0" class="form-control"
                    style="color:#000000;">
            </form>

            <!-- Button -->
            <div class="col-12 btNext mt-5 d-flex justify-content-center align-items-center">
                <button type="submit" form="balanceForm" class="btn btn-warning mb-3" name="submit">Next</button>
            </div>

        </div>
    </div>

    <script>
        const currencySymbol = "<?= $symbol ?>";
        const input = document.getElementById("balanceInput");

        input.addEventListener("input", function () {
            let v = this.value.replace(/[^0-9.]/g, "");
            if (v === "") { this.value = ""; return; }
            this.value = currencySymbol + Number(v).toLocaleString("en-PH");
        });
    </script>

</body>

</html>