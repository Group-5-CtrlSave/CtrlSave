<?php

session_start();

if (isset($_POST['btnLogin'])) {

    $emailUsername = trim($_POST['emailUsername']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT userID, userName, email, password 
                            FROM tbl_users 
                            WHERE BINARY email = ? OR BINARY userName = ? 
                            LIMIT 1");
    $stmt->bind_param("ss", $emailUsername, $emailUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['userID'] = $row['userID'];
            $_SESSION['userName'] = $row['userName'];
            $_SESSION['email']   = $row['email'];

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
