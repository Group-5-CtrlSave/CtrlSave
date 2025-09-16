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
            min-height: 100%;
            background: #ffffff;
            position: relative;
        }

        .header {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
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
            top: 170px;
            left: 0;
            width: 100%;
            height: 77vh;
            background: url('../../assets/img/landing&ads/waveUpHalf.png') no-repeat center top;
            background-size: cover;
            z-index: 1;
        }

        .content {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 85vh;
            z-index: 2;
            padding: 0 20px;
            color: #ffffff;
            top: 100px;
            overflow: hidden;
        }

        .formContent {
            margin-top: 150px;
            height: 85vh;
            font-family: "Roboto", sans-serif;
        }

        .signUpWord {
            font-family: "Poppins", sans-serif;
            font-weight: Bold;
            font-size: clamp(1.5rem, 2vw, 1rem);
            color: #ffffff;
        }

        .label {
           font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: clamp(1.3rem, 1vw, 1rem);
            color: #ffff;
        }

        .form-control {
            height: 50px;
            border: 2px solid #F6D25B;
            background-color: #F0F1F6;
        }

        .forms {
            height: 40%;
            overflow: scroll;
            
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
        <img class="img-fluid" src="../../assets/img/shared/logoName_S.png" alt="CtrlSave Logo" class="logo">
    </div>

    <!-- Background wave -->
    <div class="wave"></div>

    <!-- Forms Content -->
    <div class="content">
        <div class="container-fluid formContent">

            <!-- Title -->
            <div class="col mt-3 mb-4">
                <h3 class="signUpWord">Sign Up</h3>
            </div>

            <!-- Forms -->
            <div class="container-fluid forms">

                <div class="col-12 username mt-3">
                    <h5 class="label">Username</h5>
                    <input type="text" class="form-control" placeholder="Username" required>
                </div>

                <div class="col-12 firstname mt-3">
                    <label class="label">First Name</label>
                    <input type="text" class="form-control" placeholder="First Name" required>
                </div>

                <div class="col-12 lastname mt-3">
                    <label class="label">Last Name</label>
                    <input type="text" class="form-control" placeholder="Last Name" required>
                </div>

                <div class="col-12 email mt-3">
                    <label class="label">Email</label>
                    <input type="text" class="form-control" placeholder="Email" required>
                </div>

                <div class="col-12 password mt-3">
                    <label class="label">Password</label>
                    <input type="text" class="form-control" placeholder="Password" required>
                </div>

            </div>
            <!-- Buttons -->
            <div class="col-12 btnLogin mt-4 d-flex justify-content-center align-items-center">
                <a href="currencyLanguage.php"><button type="submit" class="btn btn-warning">Next</button></a>
            </div>

            <!-- Back to Login -->
            <div class="col-12 mb-3 mt-2 noAccount d-flex justify-content-center align-items-center">
                <a href="login.php" class="signupLink" style="color: #141313;">Back to login</a>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>