<?php

session_start();

if (isset($_POST['btnLogin'])) {

    $emailUsername = trim($_POST['emailUsername']);
    $password      = trim($_POST['password']);

    // Escape inputs to avoid SQL injection
    $emailUsernameEsc = $conn->real_escape_string($emailUsername);

    // Query without prepared statements
    $sql = "
        SELECT userID, userName, email, password
        FROM tbl_users
        WHERE BINARY email = '$emailUsernameEsc'
           OR BINARY userName = '$emailUsernameEsc'
        LIMIT 1
    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['userID']   = $row['userID'];
            $_SESSION['userName'] = $row['userName'];
            $_SESSION['email']    = $row['email'];

            header("Location: ../home/home.php");
            exit;
        } else {
            $error = "Incorrect Password";
        }

    } else {
        $error = "User not found. Incorrect Email/Username.";
    }
}
?>
