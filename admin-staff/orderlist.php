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
        <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
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

<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 10px; padding: 20px;">
            <div class="modal-header text-center">
                <h3 class="modal-title" id="orderModalLabel">Order Details</h3>
                <!-- Circle -->
                <div style="width: 20px; height: 20px; background-color: black; border-radius: 50%; margin: 0 auto; margin-left: 0%; position: relative; top: -35px;"></div>    
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-4">
                    <div class="text-center">
                        <div class="order-details">
                            <p><strong>Order:</strong> <span id="orderDetails" class="float-right"></span></p>
                            <div class="order-item">
                                <div class="order-row">
                                <p>2x Salted Caramel (22oz)<br>Dine In<br>Cash</p>
                            </div>
                            <div class="order-row">
                                <p>2x Cafe Mocha (22oz)<br>Dine In<br>Cash</p>
                            </div>
                        </div>
                        </div>
                        <p><strong></strong> <span id="orderAmount"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <hr style="width: 100%;">
                <p><strong>Ticket No:</strong> 224</p>
                <p><strong>Name:</strong> Juan</p>
                <p><strong>Total:</strong> ₱480</p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p id="confirmationText">Are you sure?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-primary" id="confirmActionBtn">Yes</button>
        </div>
      </div>
    </div>
  </div>
  
            
            

<script src="Javascript-admin/orderlist.js"></script>
<script src="Css-admin/bootstrap.bundle.min.js"></script>
</body>
</html>

