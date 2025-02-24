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
    <title>Completed Orders</title>

    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="resources/favicon/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="resources/favicon/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="resources/favicon/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="resources/favicon/favicon_io/site.webmanifest"> 

    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="Css-admin/completed_orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Add Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="Css-admin/datepicker-custom.css">
    <link rel="stylesheet" href="Css-admin/search-bar.css">
    
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
            <div class="filters" style="display: flex; justify-content: center; gap: 20px; margin: 20px 0;">
                <div class="date-filter">
                    <div class="input-group date" data-provide="datepicker" style="width: 250px;">
                        <input type="text" class="form-control" id="datepicker" placeholder="Select Date" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="search-filter">
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="ticketSearch" placeholder="Search Ticket Number" autocomplete="off">
                        <div class="input-group-append">
                            <button class="input-group-text" id="clearSearch" title="Clear search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="sales-item"><span>Date:</span> <span class="formatted-date"></span></div>
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
    <!-- Add jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="Javascript-admin/completed_orders.js"></script>
    <script src="Javascript-admin/mobile-menu.js"></script>
</body>
</html>
