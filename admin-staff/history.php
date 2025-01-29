<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Redirect to the new sales report page
header("Location: sales_report.php");
exit();
?>
