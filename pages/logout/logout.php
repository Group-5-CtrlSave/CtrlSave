<?php
// Start session
session_start();

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