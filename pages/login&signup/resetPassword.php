<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Reset Password</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;600&display=swap"
        rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: white;
            position: relative;
            overflow: hidden;
        }

        .header {
            position: absolute;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 1;
        }

        .wave {
            position: absolute;
            top: 265px;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: url('../../assets/img/login&signup/waveUpHalf.png') center top;
            background-size: cover;
            z-index: 2;
        }

        .formContainer {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100vh;
            z-index: 3;
            padding: 0 20px;
        }

        .formRow {
            margin-top: 420px;
        }

        h4 {
            color: white;
            font-family: "Poppins", sans-serif;
            margin-top: 5px;
            font-weight: bold;
        }

        p {
            color: white;
            font-family: "Roboto", sans-serif;
            font-size: 16px;
        }

        .label {
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: #ffff;
        }

        .form-control {
            border: 2px solid #F6D25B;
            height: 50px;
            font-family: "Roboto", sans-serif;
            background-color: #F0F1F6;
            border-radius: 20px;
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

        .back {
            margin-top: 15px;
        }

        @media screen and (min-width:344px) {
             .formRow {
                margin-top: 470px;
            }

            h4 {
                margin-top: 2px;
            }

            p {
                font-size: 16px;
            }

            .form-control {
                height: 50px;
            }

            .btn {
                margin-top: 50px;
                width: 150px;
                font-size: 16px;
            }

            .back {
            margin-top:20px;
            }
        }

         @media screen and (min-width:360px) {
             .formRow {
                margin-top: 420px;
            }

            h4 {
                margin-top: 2px;
            }

            p {
                font-size: 16px;
            }

            .form-control {
                height: 50px;
            }

            .btn {
                margin-top: 30px;
                width: 150px;
                font-size: 16px;
            }

            .back {
            margin-top:20px;
            }
        }

        @media screen and (min-width:375px) {
            .formRow {
                margin-top: 400px;
            }

            h4 {
                margin-top: 2px;
            }

            p {
                font-size: 14px;
            }

            .form-control {
                height: 40px;
            }

            .btn {
                margin-top: 15px;
            }

            .back {
            margin-top: 5px;
            }
        }

        @media screen and (min-width:390px) {
            .formRow {
                margin-top: 470px;
            }

            h4 {
                margin-top: 2px;
            }

            p {
                font-size: 16px;
            }

            .form-control {
                height: 50px;
            }

            .btn {
                margin-top: 50px;
            }

            .back {
            margin-top:20px;
            }
        }

        @media screen and (min-width:414px) {
            .formRow {
                margin-top: 470px;
            }

            h4 {
                margin-top: 5px;
            }

            p {
                font-size: 16px;
            }

            .form-control {
                height: 50px;
            }

            .btn {
                margin-top: 50px;
            }

            .back {
            margin-top:20px;
            }
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <div class="header p-5">
        <img class="img-fluid" src="../../assets/img/shared/logoName_L.png" alt="CtrlSave Logo">
    </div>

    <!-- Bg Design -->
    <div class="fixed-bottom wave"></div>

    <!-- Content -->
    <div class="container-fluid formContainer">
        <div class="row formRow">
            <div class="col-12 text-center">
                <h4>Reset Password</h4>
                <p>
                    Enter your registered email, and we'll send you a reset code.
                </p>
            </div>

            <!-- Email Field -->
            <div class="col-12">
                <h5 class="label">Email</h5>
                <input type="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <!-- Send Code Button -->
            <div class="col-12 d-flex justify-content-center align-items-center">
                <button type="button" class="btn">Send Code</button>
            </div>

            <!-- Back to Login -->
            <div class="col-12 text-center back">
                <a href="login.php" class="text-decoration-none "
                    style="color: #ffff; font-family: Poppins, sans-serif;">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>
</body>

</html>