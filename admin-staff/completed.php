<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CompletedOrders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Css-admin/completed.css">
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
                <li><a href="orderlist.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
                <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
                <li class="active"><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
                <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Reports</a></li>
                <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> Dashboard</a></li>
                <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
              </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="text-wrapper-10">Completed Orders</div>
            <div class="navbar">
                <div class="navbar-item">Product ID</div>
                <div class="navbar-item">Order No</div>
                <div class="navbar-item">Name</div>
                <div class="navbar-item">Eating Option</div>
                <div class="navbar-item">Order</div>
                <div class="navbar-item">Payment</div>
                <div class="navbar-item">Time</div>
                <div class="navbar-item">Cost</div>
                <div class="navbar-item">Discount</div>
                <div class="navbar-item">Total</div>
            </div>

            <div class="order-row">
                <div class="order-item">001<br />003</div>
                <div class="order-item">224</div>
                <div class="order-item">Juan</div>
                <div class="order-item">Dine In</div>
                <div class="order-item">2 x Salted Caramel 22oz<br />2 x Cafe Mocha 22oz</div>
                <div class="order-item">Cash</div>
                <div class="order-item">01:12</div>
                <div class="order-item">₱480</div>
                <div class="order-item">10%<br>10%</div>
                <div class="order-item">₱384.00</div>
            </div>

            <div class="order-row">
                <div class="order-item">004</div>
                <div class="order-item">223</div>
                <div class="order-item">Balong</div>
                <div class="order-item">Takeout</div>
                <div class="order-item">1 x Spanish Latte 22oz</div>
                <div class="order-item">GCash</div>
                <div class="order-item">01:04</div>
                <div class="order-item">₱120</div>
                <div class="order-item">0</div>
                <div class="order-item">₱120.00</div>
            </div>

            <div class="sales-summary">
                <div class="summary-left">
                    <div class="sales-item"><span>Total Orders:</span> 2</div>
                </div>
                <div class="summary-right">
                    <div class="sales-item"><span>Cash Sales:</span> ₱384.00</div>
                    <div class="sales-item"><span>GCash Sales:</span> ₱120.00</div>
                    <br>
                    <div class="sales-item"><span>Total Sales:</span> ₱504.00</div>
                </div>
            </div>
            
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
