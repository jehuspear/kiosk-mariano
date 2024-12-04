<?php
session_start();

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
  <title>Menu Management Screen</title>
  
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="Css-admin/orderlist.css">
  
  <!-- Font Awesome Icons -->
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
        <li><a href="login.php"><i class="fa-solid fa-user"></i> Login</a></li>
        <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
        <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
        <li class="active"><a href="orderlist.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
        <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
        <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Reports</a></li>
        <li><a href="decodingscreen.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
      </ul>
    </div>

    <main class="content">
        <h2>List of orders</h2>
        <div class="order-list">
            <div class="order-card">
                <div class="order-info">
                    <p class="ticket-number">Order Ticket No</p>
                    <h3>224</h3>
                    <p>For Juan</p>
                    <p>Payment Total: ₱350</p>
                    <button class="view-order">View order list</button>
                </div>
                <div class="order-actions">
                    <button class="action-btn decline">✖</button>
                    <button class="action-btn approve">✔</button>
                </div>
            </div>
            <div class="order-card">
                <div class="order-info">
                    <p class="ticket-number">Order Ticket No</p>
                    <h3>225</h3>
                    <p>For Pablo</p>
                    <p>Payment Total: ₱120</p>
                    <button class="view-order">View order list</button>
                </div>
                <div class="order-actions">
                    <button class="action-btn decline">✖</button>
                    <button class="action-btn approve">✔</button>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="Javascript-admin/orderlist.js"></script>
</body>
</html>

