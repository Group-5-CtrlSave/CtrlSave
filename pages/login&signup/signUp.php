<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Signup</title>
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
            top: 165px;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: url('../../assets/img/landing&ads/waveUpHalf.png') no-repeat center top;
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

        .title {
            margin-top: 300px;
        }

        .signUpWord {
            font-family: "Poppins", sans-serif;
            font-weight: Bold;
            font-size: 30px;
            color: #ffffff;
        }

        .formRow {
            overflow: scroll;
            height: 380px;
            margin-top: 10px;
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
            margin-top: 40px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        .back {
            color: black;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            padding-bottom: 15px;
            font-size: 16px;
        }

        /* Media Queries of Every Mobile Screen */

        @media screen and (min-width:360px) {
             .title {
                margin-top: 280px;
            }

            .signUpWord {
                font-family: "Poppins", sans-serif;
                font-weight: Bold;
                font-size: 25px;
                color: #ffffff;
            }

            .formRow {
                overflow: scroll;
                height:300px;
                margin-top: 10px;
            }

            h5 {
                font-family: "Poppins", sans-serif;
                font-weight: 700;
                color: #ffff;
            }

            .form-control {
                height: 40px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }
        }

        @media screen and (min-width:375px) {
            .title {
                margin-top: 260px;
            }

            .signUpWord {
                font-family: "Poppins", sans-serif;
                font-weight: Bold;
                font-size: 25px;
                color: #ffffff;
            }

            .formRow {
                overflow: scroll;
                height: 180px;
                margin-top: 10px;
            }

            h5 {
                font-family: "Poppins", sans-serif;
                font-weight: 700;
                color: #ffff;
            }

            .form-control {
                height: 40px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }

        }

        @media screen and (min-width:390px) {
            .title {
                margin-top: 300px;
            }

            .signUpWord {
                font-family: "Poppins", sans-serif;
                font-weight: Bold;
                font-size: 25px;
                color: #ffffff;
            }

            .formRow {
                overflow: scroll;
                height: 310px;
                margin-top: 5px;
            }

            .form-control {
                height: 50px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }
        }

        @media screen and (min-width:412px) {
             .title {
                margin-top: 320px;
            }

            .signUpWord {
                font-family: "Poppins", sans-serif;
                font-weight: Bold;
                font-size: 25px;
                color: #ffffff;
            }

            .formRow {
                overflow: scroll;
                height: 360px;
                margin-top: 10px;
            }

            h5 {
                font-family: "Poppins", sans-serif;
                font-weight: 700;
                color: #ffff;
            }

            .form-control {
                height: 40px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }
        }

        @media screen and (min-width:414px) {
            .title {
                margin-top: 320px;
            }

            .signUpWord {
                font-family: "Poppins", sans-serif;
                font-weight: Bold;
                font-size: 25px;
                color: #ffffff;
            }

            .formRow {
                overflow: scroll;
                height: 300px;
                margin-top: 10px;
            }

            .form-control {
                height: 50px;
                font-family: "Roboto", sans-serif;
                background-color: white;
            }
        }

        @media screen and (min-width:430px) {
            .title {
                margin-top: 320px;
            }

            .signUpWord {
                font-family: "Poppins", sans-serif;
                font-weight: Bold;
                font-size: 25px;
                color: #ffffff;
            }

            .formRow {
                overflow: scroll;
                height: 391px;
                margin-top: 10px;
            }

            .form-control {
                height: 50px;
                font-family: "Roboto", sans-serif;
                background-color: white
            }
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <div class="header p-5">
        <img class="img-fluid" src="../../assets/img/shared/logoName_S.png" alt="CtrlSave Logo" class="logo">
    </div>

    <!-- Bg Design -->
    <div class="fixed-bottom wave"></div>

    <!-- Content -->
    <div class="container-fluid formContainer">

        <!-- Row for Title -->
        <div class="row title">
            <div class="col-12">
                <h3 class="signUpWord">Sign Up</h3>
            </div>
        </div>

        <!-- Row for Forms -->
        <div class="row formRow">
            <!-- Username -->
            <div class="col-12 forms mt-3">
                <h5>Username</h5>
                <input type="text" class="form-control" placeholder="Username" required>
            </div>

            <!-- First name -->
            <div class="col-12 forms mt-3">
                <h5>First Name</h5>
                <input type="text" class="form-control" placeholder="First Name" required>
            </div>

            <!-- Last Name -->
            <div class="col-12 forms mt-3">
                <h5>Last Name</h5>
                <input type="text" class="form-control" placeholder="Last Name" required>
            </div>

            <!-- Email -->
            <div class="col-12 forms mt-3">
                <h5>Email</h5>
                <input type="text" class="form-control" placeholder="Email" required>
            </div>

            <!-- Password -->
            <div class="col-12 forms mt-3">
                <h5>Password</h5>
                <input type="text" class="form-control" placeholder="Password" required>
            </div>

        </div>

        <!-- Row for buttons -->
        <div class="row buttonRow">
            <!-- Button -->
            <div class="col-12 btnLogin d-flex justify-content-center align-items-center">
                <a href="currency.php"><button type="submit" class="btn btn-warning mb-3">Next</button></a>
            </div>

            <!-- Sign Up -->
            <div class="col-12 noAccount mt-1 d-flex justify-content-center align-items-center">
                <a href="login.php" class="back" style="color: black;">back to login</a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>