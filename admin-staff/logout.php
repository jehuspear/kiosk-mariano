<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear remember me cookies if they exist
if(isset($_COOKIE['user_login'])) {
    setcookie("user_login", "", time() - 3600, "/");
}
if(isset($_COOKIE['user_password'])) {
    setcookie("user_password", "", time() - 3600, "/");
}

// Redirect to login page
header("Location: login.php");
exit();
?>