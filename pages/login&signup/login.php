<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .header {
            position: absolute;
            top: 50px;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-right: 10px;
            margin-top: 15px;
        }

        .wave {
            position: absolute;
            top: 290px;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../../assets/img/login&signup/waveUpHalf.png') no-repeat center top;
            background-size: cover;
            z-index: 1;
        }

        .content {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100vh;
            z-index: 2;
            padding: 0 20px;
            top: 100px;
        }

        .formContent {
            margin-top: 310px;
        }

        .label {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: clamp(1.3rem, 1vw, 1rem);
            color: #ffff;
        }

        .form-control {
            border-color: #F6D25B;
            height: 50px;
            font-family: "Roboto", sans-serif;
            background-color: #F0F1F6;
        }

        .btn {
            background-color: #F6D25B;
            color: black;
            text-align: center;
            width: 150px;
            font-size: clamp(1.3rem, 2vw, 1rem);
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 30px;
            cursor: pointer;
            z-index: 2;
            text-decoration: none;
            border: none;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        .signupLink {
            color: black;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            padding-bottom: 5px;
        }

    </style>
</head>

<body>
    <!-- Logo -->
    <div class="header">
        <img class="img-fluid" src="../../assets/img/shared/logoName_L.png" alt="CtrlSave Logo" class="logo">
    </div>

    <!-- Bg Design -->
    <div class="wave"></div>

    <!-- Content -->
    <div class="content">
        <div class="container-fluid formContent">

            <!-- Email -->
            <div class="col-12 email mt-3">
                <h5 class="label" style="font-family: Poppins, sans-serif;">Email/Username</h5>
                <input type="text" class="form-control" placeholder="Email/Username" required>
            </div>

            <!-- Password -->
            <div class="col-12 password mt-3 mb-3">
                <h5 class="label" style="font-family: Poppins, sans-serif;">Password</h5>
                <input type="text" class="form-control" placeholder="Password" required>
            </div>

            <!-- Button -->
            <div class="col-12 btnLogin mt-4 mb-2 d-flex justify-content-center align-items-center">
                <a href="../home/home.php"><button type="submit" class="btn btn-warning mb-3">Login</button></a>
            </div>

            <!-- Sign Up -->
            <div class="col-12 noAccount mt-1 d-flex justify-content-center align-items-center">
                <h5 style="color: #ffff;">Don't have an account?</h5>&nbsp;<a href="../landing&ads/firstAd.html" class="signupLink"
                    style="color: black;">Sign Up</a>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>