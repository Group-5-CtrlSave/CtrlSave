<?php
session_start();
require_once('../../assets/shared/connect.php');

// Check if reset session exists
if (!isset($_SESSION['reset_data'])) {
    $_SESSION['msg']['error'] = "No reset request found.";
    header('Location: resetPassword.php');
    exit;
}

// Check expiry
if (time() > $_SESSION['reset_data']['expires']) {
    unset($_SESSION['reset_data']);
    $_SESSION['msg']['error'] = "Reset code expired.";
    header('Location: resetPassword.php');
    exit;
}

// Check max attempts
if ($_SESSION['reset_data']['attempts'] >= $_SESSION['reset_data']['max_attempts']) {
    unset($_SESSION['reset_data']);
    $_SESSION['msg']['error'] = "Too many failed attempts.";
    header('Location: resetPassword.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input
    $resetCode = preg_replace('/[^0-9]/', '', $_POST['reset_code'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Password match
    if ($newPassword !== $confirmPassword) {
        $_SESSION['msg']['error'] = "Passwords do not match.";
        header('Location: resetCode.php');
        exit;
    }

    // Password length
    if (strlen($newPassword) < 8) {
        $_SESSION['msg']['error'] = "Password must be at least 8 characters.";
        header('Location: resetCode.php');
        exit;
    }

    // Verify reset code
    if (!password_verify($resetCode, $_SESSION['reset_data']['code'])) {
        $_SESSION['reset_data']['attempts']++;
        $remaining = $_SESSION['reset_data']['max_attempts'] - $_SESSION['reset_data']['attempts'];
        usleep(500000); // slight delay to prevent brute force
        $_SESSION['msg']['error'] = $remaining > 0 ? "Invalid code. $remaining attempt(s) left." : "Too many attempts.";
        header('Location: resetCode.php');
        exit;
    }

    // Update password in database
    $userID = $_SESSION['reset_data']['userID'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE tbl_users SET password = ? WHERE userID = ?");
    $stmt->bind_param("si", $hashedPassword, $userID);

    if ($stmt->execute()) {
        // Clean up session
        unset($_SESSION['reset_data']);
        unset($_SESSION['reset_code_display']);
        session_regenerate_id(true);

        $_SESSION['msg']['success'] = "Password reset successful!";
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['msg']['error'] = "Failed to update password. Please try again.";
        header('Location: resetCode.php');
        exit;
    }
}

// Timer for countdown
$remainingTime = $_SESSION['reset_data']['expires'] - time();
$remainingMinutes = floor($remainingTime / 60);
$remainingSeconds = $remainingTime % 60;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Reset Code</title>
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #44B87D; }
        .toast-message { position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            padding: 10px 18px; border-radius: 20px; width: 300px;
            font-family: "Poppins", sans-serif; font-size: 14px; font-weight: 600;
            z-index: 9999; text-align: center;
        }
        .toast-error { background-color: #E63946; color: white; }
        .toast-success { background-color: #F6D25B; color: black; }
        h2 { font-family: "Poppins", sans-serif; font-weight: bold; color: #fff; text-align: center; }
        .form-control {
            border: 2px solid #F6D25B; height: 50px;
            font-family: "Roboto", sans-serif; background-color: #F0F1F6; border-radius: 15px;
        }
        .label { color: #fff; font-family: "Poppins", sans-serif; font-weight: 600; margin-top: 15px; margin-bottom: 5px; }
        .btn {
            background-color: #F6D25B; color: black; width: 125px; font-size: 20px;
            font-weight: bold; font-family: "Poppins", sans-serif; border-radius: 27px;
            border: none; margin-top: 30px;
        }
        .btn:hover { box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24); }
        .password-wrapper { position: relative; }
        .password-wrapper input.form-control { padding-right: 48px; }
        .toggle-password {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: transparent; border: none; cursor: pointer; color: #44B87D;
        }
        .timer {
            background-color: rgba(255, 255, 255, 0.2); color: white;
            padding: 8px 16px; border-radius: 20px; font-family: "Poppins", sans-serif;
            font-size: 14px; font-weight: 600; margin-top: 10px; display: inline-block;
        }
        .attempts-info { color: #FFC107; font-family: "Poppins", sans-serif; font-size: 12px; margin-top: 5px; }
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

    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="resetPassword.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;" />
                </a>
            </div>
            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center" style="color:black;">Reset Password</h2>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Enter Reset Code</h2>
        <div class="text-center mb-3">
            <div class="timer" id="timer">
                Expires in: <?php echo sprintf("%02d:%02d", $remainingMinutes, $remainingSeconds); ?>
            </div>
            <p class="attempts-info">
                Attempts: <?php echo $_SESSION['reset_data']['max_attempts'] - $_SESSION['reset_data']['attempts']; ?>/<?php echo $_SESSION['reset_data']['max_attempts']; ?>
            </p>
        </div>

        <form method="POST">
            <label class="label">Reset Code (Check your email)</label>
            <input type="text" name="reset_code" class="form-control" placeholder="6-digit code" maxlength="6" pattern="[0-9]{6}" required>

            <label class="label">New Password</label>
            <div class="password-wrapper">
                <input id="new_password" type="password" name="new_password" class="form-control" placeholder="Min 8 characters" minlength="8" required>
                <button type="button" id="toggleNew" class="toggle-password">👁️</button>
            </div>

            <label class="label">Confirm Password</label>
            <div class="password-wrapper">
                <input id="confirm_password" type="password" name="confirm_password" class="form-control" placeholder="Confirm password" minlength="8" required>
                <button type="button" id="toggleConfirm" class="toggle-password">👁️</button>
            </div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn">Submit</button>
            </div>
        </form>
    </div>

    <script>
        let timeRemaining = <?php echo $remainingTime; ?>;
        const timerEl = document.getElementById('timer');
        setInterval(() => {
            if (timeRemaining <= 0) {
                alert('Code expired!');
                window.location.href = 'resetPassword.php';
                return;
            }
            timeRemaining--;
            const min = Math.floor(timeRemaining / 60);
            const sec = timeRemaining % 60;
            timerEl.textContent = `Expires in: ${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
        }, 1000);

        document.getElementById('toggleNew').addEventListener('click', function() {
            const pwd = document.getElementById('new_password');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        });

        document.getElementById('toggleConfirm').addEventListener('click', function() {
            const pwd = document.getElementById('confirm_password');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        });
    </script>
</body>
</html>
