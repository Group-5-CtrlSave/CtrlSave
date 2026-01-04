<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('../../assets/shared/connect.php'); 
require '../../assets/PHPMailer-master/src/Exception.php';
require '../../assets/PHPMailer-master/src/PHPMailer.php';
require '../../assets/PHPMailer-master/src/SMTP.php';

// Security: Regenerate session ID to prevent session fixation
if (!isset($_SESSION['reset_session_initialized'])) {
    session_regenerate_id(true);
    $_SESSION['reset_session_initialized'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $_SESSION['msg']['error'] = "Invalid email address.";
        header('Location: resetPassword.php');
        exit;
    }

    // Rate limiting: Prevent brute force attempts
    if (isset($_SESSION['last_reset_attempt'])) {
        $time_diff = time() - $_SESSION['last_reset_attempt'];
        if ($time_diff < 60) { // 1 minute cooldown
            $_SESSION['msg']['error'] = "Please wait " . (60 - $time_diff) . " seconds before trying again.";
            header('Location: resetPassword.php');
            exit;
        }
    }

    $stmt = $conn->prepare("SELECT userID, email FROM tbl_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['msg']['success'] = "If this email exists, a reset code has been sent.";
        header('Location: resetPassword.php');
        exit;
    }

    $user = $result->fetch_assoc();

    $resetCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

    $_SESSION['reset_data'] = [
        'code' => password_hash($resetCode, PASSWORD_DEFAULT),
        'email' => $email,
        'userID' => $user['userID'],
        'expires' => time() + 900, // 15 minutes
        'attempts' => 0,
        'max_attempts' => 3
    ];

    $_SESSION['last_reset_attempt'] = time();

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ctrlsave.gv@gmail.com'; //username
        $mail->Password   = 'hcpf npka tpru bvag'; //password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@example.com', 'CtrlSave');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'CtrlSave Password Reset Code';
        $mail->Body    = "<p>Dear user,</p>
                          <p>Your password reset code is: <b>$resetCode</b></p>
                          <p>This code will expire in 15 minutes.</p>";

        $mail->send();
        $_SESSION['msg']['success'] = "A reset code has been sent to your email.";
    } catch (Exception $e) {
        $_SESSION['msg']['error'] = "Failed to send email. Please try again later.";
    }

    header('Location: resetCode.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave | Reset Password</title>
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: white;
            position: relative;
            overflow: hidden;
        }

        /* Error/Success Toast */
        .toast-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
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

        .toast-error {
            background-color: #E63946;
            color: white;
        }

        .toast-success {
            background-color: #44B87D;
            color: white;
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
            background: url('../../assets/img/login&signup/waveUpHalf.svg') center top;
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
                margin-top: 20px;
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
                margin-top: 20px;
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
                margin-top: 20px;
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
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['msg']['error'])) { ?>
        <div class="toast-message toast-error">
            <?php echo htmlspecialchars($_SESSION['msg']['error']); unset($_SESSION['msg']['error']); ?>
        </div>
    <?php } ?>

    <?php if (isset($_SESSION['msg']['success'])) { ?>
        <div class="toast-message toast-success">
            <?php echo htmlspecialchars($_SESSION['msg']['success']); unset($_SESSION['msg']['success']); ?>
        </div>
    <?php } ?>

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
            <form method="POST">
                <!-- Email Field -->
                <div class="col-12">
                    <h5 class="label">Email</h5>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required autocomplete="email">
                </div>
                <!-- Send Code Button -->
                <div class="col-12 d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn" name="sendCode">Send Code</button>
                </div>
            </form>
            <!-- Back to Login -->
            <div class="col-12 text-center back">
                <a href="login.php" class="text-decoration-none" style="color: #ffff; font-family: Poppins, sans-serif;">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>