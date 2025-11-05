<?php

session_start();

$error = "";

if (isset($_POST['signup'])) {

    $username = trim($_POST['username']);
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email already exists
    $check = $conn->prepare("SELECT email FROM tbl_users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult && $checkResult->num_rows > 0) {
        $error = "Email already registered.";
    } else {

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $insert = $conn->prepare("INSERT INTO tbl_users (userName, firstName, lastName, email, password) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $username, $fname, $lname, $email, $hashedPassword);

        if ($insert->execute()) {

            // ✅ Store the new user's ID to session
            $_SESSION['userID'] = $insert->insert_id;

            // ✅ Redirect to currency page after sign up
            header("Location: currency.php");
            exit();
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>
