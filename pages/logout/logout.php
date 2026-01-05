<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include ("../../assets/shared/connect.php");
?>

<?php
// Start session
session_start();

if (isset($_COOKIE['remember_me'])) {
    $token = hash('sha256', $_COOKIE['remember_me']);

    $deleteTokenQuery = "DELETE FROM tbl_usertokens WHERE token = '$token'";
    executeQuery($deleteTokenQuery);

    setcookie("remember_me", "", time() - 3600, "/");
}

// Destroy all session data
session_unset();
session_destroy();

// Prevent the browser from caching pages like history, notif, etc.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

//Redirect to login
header("Location: ../../pages/login&signup/login.php");
exit;
?>