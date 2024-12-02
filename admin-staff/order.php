<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orderscreen</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- External CSS Link -->
        <link rel="stylesheet" href="Css-admin/order.css">
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
        <li class="active"><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
        <li class="completed"><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i><span>Completed</span>
        </a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Reports</a></li>
        <li><a href="decodingscreen.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="text-wrapper-10">Pending Orders</div>
                <div class="navbar">
                    <div class="navbar-item">Product ID</div>
                    <div class="navbar-item">Order No</div>
                    <div class="navbar-item">Name</div>
                    <div class="navbar-item">Eating Option</div>
                    <div class="navbar-item">Order</div>
                    <div class="navbar-item">Payment</div>
                    <div class="navbar-item">Date</div>
                    <div class="navbar-item">Discount</div>
                    <div class="navbar-item">Total</div>
                    <div class="navbar-item">Status</div>
                </div>

                <!-- Order Rows -->
                <div class="order">
                    <div class="order-item">001<br />003</div>
                    <div class="order-item">224</div>
                    <div class="order-item">Juan</div>
                    <div class="order-item">Dine In</div>
                    <div class="order-item">2 x Salted Caramel 22oz<br />2 x Cafe Mocha 22oz</div>
                    <div class="order-item">Cash</div>
                    <div class="order-item">12/10/24</div>
                    <div class="order-buttons">
                        <button class="button">Add</button>
                        <button class="button">Add</button>
                    </div>
                    <div class="order-item">₱480</div>
                    <div class="order-buttons">
                        <button class="button">Done</button>
                    </div>
                </div>

                <div class="order">
                    <div class="order-item">002</div>
                    <div class="order-item">223</div>
                    <div class="order-item">Balong</div>
                    <div class="order-item">Takeout</div>
                    <div class="order-item">1 x Spanish Latte 22oz</div>
                    <div class="order-item">GCash</div>
                    <div class="order-item">12/10/24</div>
                    <div class="order-buttons">
                        <button class="button">Add</button>
                    </div>
                    <div class="order-item">₱120</div>
                    <div class="order-buttons">
                        <button class="button">Done</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>






