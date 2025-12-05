<?php
include("../../pages/login&signup/process/signupBE.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Signup</title>
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: white;
            position: relative;
            overflow: hidden;
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


        /* Password toggle */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input.form-control {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #44B87D;
            outline: none;
        }

        .toggle-password svg {
            display: block;
        }

        .toggle-password:focus {
            box-shadow: none;
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
                height: 260px;
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
    <!-- ✅ Toast Message -->
    <?php if (!empty($error)) { ?>
        <div id="errorToast"><?php echo $error; ?></div>
    <?php } ?>

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

        <form method="POST">
            <!-- Row for Forms -->
            <div class="row formRow">


                <!-- Username -->
                <div class="col-12 forms mt-3">
                    <h5>Username</h5>
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>

                <!-- First name -->
                <div class="col-12 forms mt-3">
                    <h5>First Name</h5>
                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
                </div>

                <!-- Last Name -->
                <div class="col-12 forms mt-3">
                    <h5>Last Name</h5>
                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
                </div>

                <!-- Email -->
                <div class="col-12 forms mt-3">
                    <h5>Email</h5>
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>

                <!-- Password -->
                <div class="col-12 forms mt-3">
                    <h5>Password</h5>
                    <div class="password-wrapper">
                        <input id="password" type="password" class="form-control" name="password" placeholder="Password"
                            required pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                            title="Password must be at least 8 characters with uppercase, lowercase, number, and special character.">
                        <button type="button" id="togglePassword" class="toggle-password" aria-label="Show password"
                            title="Show password">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>

                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-5 0-9.27-3-11-8 1.13-3.15 3.67-5.67 6.6-6.77">
                                </path>
                                <path d="M1 1l22 22"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="passwordRequirements"
                        style="font-size: 13px; margin-top: 6px; color: white; font-family: 'Poppins', sans-serif;">
                        <p id="reqLength">❌ At least 8 characters</p>
                        <p id="reqUpper">❌ One uppercase letter (A–Z)</p>
                        <p id="reqLower">❌ One lowercase letter (a–z)</p>
                        <p id="reqNumber">❌ One number (0–9)</p>
                        <p id="reqSpecial">❌ One special character (@$!%*?&)</p>
                    </div>

                </div>


            </div>

            <!-- Row for buttons -->
            <div class="row buttonRow">
                <!-- Button -->
                <div class="col-12 btnLogin d-flex justify-content-center align-items-center">
                    <button type="submit" name="signup" class="btn btn-warning mb-3" id="nextBtn" disabled>Next</button>
                </div>


                <!-- Sign Up -->
                <div class="col-12 noAccount mt-1 d-flex justify-content-center align-items-center">
                    <a href="login.php" class="back" style="color: black;">back to login</a>
                </div>
            </div>
        </form>

    </div>


    <script>
        const pwd = document.getElementById("password");
        const toggle = document.getElementById("togglePassword");
        const eyeOpen = document.getElementById("eyeOpen");
        const eyeClosed = document.getElementById("eyeClosed");

        toggle.addEventListener("click", () => {
            if (pwd.type === "password") {
                pwd.type = "text";
                eyeOpen.style.display = "block";   // show open eye
                eyeClosed.style.display = "none";  // hide closed eye
            } else {
                pwd.type = "password";
                eyeOpen.style.display = "none";    // hide open eye
                eyeClosed.style.display = "block"; // show closed eye
            }
        });
    </script>

    <script>
        const password = document.getElementById("password");
        const nextBtn = document.getElementById("nextBtn");

        // Requirement text elements
        const reqLength = document.getElementById("reqLength");
        const reqUpper = document.getElementById("reqUpper");
        const reqLower = document.getElementById("reqLower");
        const reqNumber = document.getElementById("reqNumber");
        const reqSpecial = document.getElementById("reqSpecial");

        // Validation Regex
        function validatePassword(pwd) {
            return {
                length: pwd.length >= 8,
                upper: /[A-Z]/.test(pwd),
                lower: /[a-z]/.test(pwd),
                number: /\d/.test(pwd),
                special: /[@$!%*?&]/.test(pwd)
            };
        }

        // Update UI on input
        password.addEventListener("input", () => {
            const val = password.value;
            const check = validatePassword(val);

            // Update icons only — ✔ white, ❌ black
            reqLength.innerHTML = (check.length ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " At least 8 characters";
            reqUpper.innerHTML = (check.upper ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One uppercase letter (A–Z)";
            reqLower.innerHTML = (check.lower ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One lowercase letter (a–z)";
            reqNumber.innerHTML = (check.number ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One number (0–9)";
            reqSpecial.innerHTML = (check.special ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One special character (@$!%*?&)";

            // Enable button only if all valid
            if (check.length && check.upper && check.lower && check.number && check.special) {
                nextBtn.disabled = false;
                nextBtn.style.opacity = "1";
            } else {
                nextBtn.disabled = true;
                nextBtn.style.opacity = "0.6";
            }
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>