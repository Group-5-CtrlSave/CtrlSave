<?php
include("../../pages/login&signup/process/currencyBE.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave | Set Currency</title>
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #44B87D;
        }

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
                transform: translateX(-50%) translateY(-5px);
            }

            10% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }
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

        .form-group {
            margin-bottom: 10px;
            width: 100%;
            max-width: 300px;
        }

        .form-group label {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
            text-align: left;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #F6D25B;
            border-radius: 20px;
            background-color: white;
            font-size: 16px;
            color: #000000;
            appearance: none;
            background-repeat: no-repeat;
            background-position: right 10px center;
            font-family: "Roboto", sans-serif;
        }

        .form-select {
            --bs-form-select-bg-img: url("data:image/svg+xml;utf8,<svg fill='green' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path d='M1.5 5l6 6 6-6'/></svg>");
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

    <?php if (!empty($error)): ?>
        <div id="errorToast"><?php echo $error; ?></div>
    <?php endif; ?>

    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top" style="height: 75px;">
        <div class="container-fluid position-relative">
            <div class="position-absolute top-70 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Set Currency</h2>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-5" >
        <div class="row main-row text-center">

            <h2 style="color:white;">Choose Your Main Currency</h2>
            <p style="color:white;">Don't worry this can be changed later</p>

            <form method="POST">
                <div class="form-group mt-4 mb-5" style="max-width:300px; margin:auto;">
                    <label class="text-white">Common Currency</label>
                    <select class="form-select" name="currency" required>
                        <option selected disabled>Currency</option>
                        <option value="PHP">Philippine Peso (PHP)</option>
                        <option value="USD">US Dollar (USD)</option>
                    </select>
                </div>

                <button type="submit" name="setCurrency" class="btn btn-warning mt-4">Next</button>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>