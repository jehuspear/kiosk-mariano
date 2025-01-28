<?php
session_start();
include 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="Css-admin/completed_orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php 
        require_once 'includes/sidebar.php';
        renderSidebar('completed');
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Mobile Menu Toggle -->
            <div class="mobile-menu-toggle d-lg-none">
                <button class="btn btn-dark" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="text-wrapper-10" style="text-align: center;">Completed Orders</div>
            <div class="navbar">
                <div class="navbar-item">Order ID</div>
                <div class="navbar-item">Order Ticket Number</div>
                <div class="navbar-item">Eating Option</div>
                <div class="navbar-item">Menu Item ID</div>
                <div class="navbar-item">Order Item</div>
                <div class="navbar-item">Payment Method</div>
                <div class="navbar-item">Total time to complete the order <br> (MIN:SEC)</div>  <!-- from Order_DateTime   -->
                <div class="navbar-item">Cost</div>
                <div class="navbar-item">Discount</div>
                <div class="navbar-item">Total</div>
            </div>

            <div class="orders-container">
                <!-- Orders will be dynamically populated here -->
            </div>

            <div class="sales-summary">
                <div class="summary-left">
                    <div class="sales-item"><span>Total Orders:</span> <span class="total-orders">0</span></div>
                </div>
                <div class="summary-right">
                    <div class="sales-item"><span>Cash Sales:</span> <span class="cash-sales">₱0.00</span></div>
                    <div class="sales-item"><span>GCash Sales:</span> <span class="gcash-sales">₱0.00</span></div>
                    <br>
                    <div class="sales-item"><span>Total Sales:</span> <span class="total-sales">₱0.00</span></div>
                </div>
            </div>
            
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Javascript-admin/completed_orders.js"></script>
    <script src="Javascript-admin/mobile-menu.js"></script>
</body>
</html>
