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

// Get user's role and current page
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Define page access rules
$orderPages = [
    'pending-orders',
    'preparing-orders',
    'completed-orders'
];

$adminPages = [
    'home',
    'staff-management',
    'menuscreen',
    'feedback',
    'history'
];

// Check page access permissions
if (in_array($currentPage, $adminPages)) {
    // Admin pages require Admin role
    if ($userRole !== 'Admin') {
        // $_SESSION['error_message'] = 'Access denied. This page is only accessible to administrators.';
        header('Location: pending-orders.php');
        exit;
    }
} elseif (in_array($currentPage, $orderPages)) {
    // Order pages accessible to both Admin and Staff
    if ($userRole !== 'Admin' && $userRole !== 'Staff') {
        $_SESSION['error_message'] = 'Invalid user role. Please contact an administrator.';
        header('Location: logout.php');
        exit;
    }
} else {
    // Unknown pages redirect to pending orders
    $_SESSION['error_message'] = 'Page not found.';
    header('Location: pending-orders.php');
    exit;
}
?>
