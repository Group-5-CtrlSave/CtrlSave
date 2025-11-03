<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Login</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: white;
            position: relative;
            overflow: hidden;
        }

        /* Logo */
        .header {
            position: absolute;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 1;
        }

        .logo {
            width: 100%;
            margin-top: 30px;
            text-align: center;
            justify-content: center;
            align-items: center;
        }

        /* Backrground */
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

        /* Content */
        .formContainer {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100vh;
            z-index: 3;
            padding: 0 20px;
        }

        .formRow {
            margin-top: 400px;
        }

        h5 {
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            color: #ffff;
        }

        .form-control {
            border: 2px solid #F6D25B;
            height: 50px;
            font-family: "Roboto", sans-serif;
            background-color: white;
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

        p {
            font-size: 16px;
            color: white;
            font-weight: bold;
            font-family: "Poppins", sans-serif; ;
        }

        .signupLink {
            color: black;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            padding-bottom: 15px;
            font-size: 16px;
        }

        /* Media Queries of Every Mobile Screen */
        @media screen and (min-width:344px) {
            .formRow {
                margin-top: 450px;
            }

            .form-control {
                height: 40px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }

            .btn {
            margin-top: 35px;
            }

        }

        @media screen and (min-width:360px) {
            .formRow {
                margin-top: 380px;
            }

            .form-control {
                height: 45px;
            }

            .btn {
            margin-top: 30px;
            }
        }

        @media screen and (min-width:375px) {
            .formRow {
                margin-top: 360px;
            }

            .form-control {
                height: 37px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }

            .btn {
            margin-top: 15px;
            }
        }

        @media screen and (min-width:390px) {
            .formRow {
                margin-top: 430px;
            }

            .form-control {
                height: 50px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }

            .btn {
            margin-top: 40px;
            }
        }

        @media screen and (min-width:412px) {
             .formRow {
                margin-top: 430px;
            }

            .form-control {
                height: 60px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }

            .btn {
            margin-top: 40px;
            }

        }

        @media screen and (min-width:414px) {
            .formRow {
                margin-top: 440px;
            }

            .form-control {
                height: 60px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }

            .btn {
                margin-top: 40px;
            }
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <div class="header p-5">
        <img class="img-fluid" src="../../assets/img/shared/logoName_L.png" alt="CtrlSave Logo" class="logo">
    </div>

    <!-- Bg Design -->
    <div class="fixed-bottom wave"></div>

    <!-- Content -->
    <div class="container-fluid formContainer">

        <div class="row formRow">
            <!-- Email -->
            <div class="col-12 forms mt-3">
                <h5>Email/Username</h5>
                <input type="text" class="form-control" placeholder="Email/Username" required>
            </div>

            <!-- Password -->
            <div class="col-12 forms mt-3">
                <h5>Password</h5>
                <input type="text" class="form-control" placeholder="Password" required>
            </div>

            <div class="col-12 text-end mt-2">
                <a href="resetPassword.php" class="text-decoration-none"
                    style="color: #ffff; font-family: Poppins, sans-serif;">
                    Forgot Password?
                </a>
            </div>

            <!-- Button -->
            <div class="col-12 btnLogin d-flex justify-content-center align-items-center">
                <a href="../home/home.php"><button type="submit" class="btn btn-warning mb-3">Login</button></a>
            </div>

            <!-- Sign Up -->
            <div class="col-12 noAccount mt-1 d-flex justify-content-center align-items-center">
                <p style="color: #ffff;">Don't have an account?</p>&nbsp;<a href="../landing&ads/firstAd.html"
                    class="signupLink" style="color: black;">Sign Up</a>
            </div>
        </div>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>