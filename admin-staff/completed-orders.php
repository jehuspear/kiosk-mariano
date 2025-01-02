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
    <title>CompletedOrders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Css-admin/completed_orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>SINCO CAFE</h2>
            </div>
            <ul class="nav">
                <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i> <span>Logout</span></a></li>
                <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
                <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
                <li><a href="pending-orders.php"><i class="fa-solid fa-mug-hot"></i> Pending</a></li>
                <li><a href="preparing-orders.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
                <li class="active"><a href="completed-orders.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
                <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Dashboard</a></li>
                <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
                <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
              </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="text-wrapper-10">Completed Orders</div>
            <div class="navbar">
                <div class="navbar-item">Order ID</div>
                <div class="navbar-item">Order Ticket Number</div>
                <div class="navbar-item">Eating Option</div>
                <div class="navbar-item">Menu Item ID</div>
                <div class="navbar-item">Order Item</div>
                <div class="navbar-item">Payment Method</div>
                <div class="navbar-item">Total minutes to complete the order</div>  <!-- from Order_DateTime   -->
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
</body>
</html>
