<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Please log in to access this page.';
    header('Location: login.php');
    exit;
}

// Check if user has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    // $_SESSION['error_message'] = 'Access denied. This page is only accessible to administrators.';
    header('Location: pending-orders.php');
    exit;
}

// If user is logged in and is admin, redirect to sales report
header('Location: sales_report.php');
exit;
?>
