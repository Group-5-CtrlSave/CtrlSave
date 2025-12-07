<?php
include("../../pages/login&signup/process/loginBE.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Login</title>
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

        /* Fade Animation: stays visible → fades out smoothly */
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
            font-family: "Poppins", sans-serif;
            ;
        }

        .signupLink {
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
                margin-top: 365px;
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
    <?php if (isset($error)) { ?>
        <div id="errorToast">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <!-- Logo -->
    <div class="header p-5">
        <img class="img-fluid" src="../../assets/img/shared/logoName_L.png" alt="CtrlSave Logo" class="logo">
    </div>

    <!-- Bg Design -->
    <div class="fixed-bottom wave"></div>

    <!-- Content -->
    <div class="container-fluid formContainer">

        <form method="POST">
            <div class="row formRow">
                <!-- Email -->
                <div class="col-12 forms mt-3">
                    <h5>Email/Username</h5>
                    <input type="text" class="form-control" name="emailUsername" placeholder="Email/Username" required>
                </div>

                <div class="col-12 forms mt-2">
                    <h5>Password</h5>
                    <div class="password-wrapper">
                        <input id="password" type="password" class="form-control" name="password" placeholder="Password"
                            required>
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
                </div>

                <div class="col-12 text-end mt-1">
                    <a href="resetPassword.php" class="text-decoration-none"
                        style="color: #ffff; font-family: Poppins, sans-serif;">
                        Forgot Password?
                    </a>
                </div>

                <!-- Button -->
                <div class="col-12 btnLogin d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-warning mb-3" name="btnLogin">Login</button>
                </div>

                <!-- Sign Up -->
                <div class="col-12 noAccount mt-1 d-flex justify-content-center align-items-center">
                    <p style="color: #ffff;">Don't have an account?</p>&nbsp;<a href="../landing&ads/firstAd.html"
                        class="signupLink" style="color: black;">Sign Up</a>
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
        (function () {
            const body = document.body;
            const originalOverflow = body.style.overflow;
            const originalScrollPos = window.scrollY;

            document.querySelectorAll("input, textarea").forEach(input => {

                // When user taps an input (keyboard opens)
                input.addEventListener("focus", () => {

                    // Allow page to scroll ONLY while keyboard is open
                    body.style.overflowY = "auto";

                    // Scroll the input into view smoothly
                    setTimeout(() => {
                        input.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }, 250);
                });

                // When user finishes typing (keyboard closes)
                input.addEventListener("blur", () => {

                    // Re-lock scrolling
                    body.style.overflowY = "hidden";

                    // Smoothly return the page to its ORIGINAL position
                    setTimeout(() => {
                        window.scrollTo({
                            top: originalScrollPos,
                            behavior: "smooth"
                        });
                    }, 50);
                });
            });

        })();
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>