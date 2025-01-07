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
        <title>Pending Orders</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/sidebar.css">
        <link rel="stylesheet" href="Css-admin/order.css">
        <link rel="stylesheet" href="Css-admin/search_order.css">
        <link rel="stylesheet" href="Css-admin/admin-modal.css">
       
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="wrapper">
            <?php 
            require_once 'includes/sidebar.php';
            renderSidebar('pending');
            ?>
            <!-- Main Content -->
            <div class="main-content">
                <!-- Mobile Menu Toggle -->
                <div class="mobile-menu-toggle d-lg-none">
                    <button class="btn btn-dark" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="text-wrapper-10" style="text-align: center;">Pending Orders</div>
                <!-- Search Bar -->
                <div class="search-container mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchTicket" class="form-control" placeholder="Search Order Ticket Number...">
                    </div>
                </div>
                <div class="navbar">
                    
                    <div class="navbar-item">Order Ticket No</div>
                    
                    <div class="navbar-item">Eating Option</div>
                    <div class="navbar-item">Product ID</div>
                    <div class="navbar-item">Order Item</div>
                    <div class="navbar-item">Payment</div>
                    <div class="navbar-item">Date</div>
                    <div class="navbar-item">Discount</div>
                    <div class="navbar-item">Total</div>
                    <div class="navbar-item">Status</div>
                </div>

  
            <div class="order-details-container">
            <?php
            require_once 'database_admin.php';
            
            $sql = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.OrderItem_Quantity, ' x ', m.MenuItem_Name, ' ', oi.OrderItem_CupSize) SEPARATOR '<br>') as items
                    FROM `order` o
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID
                    JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
                    WHERE o.Order_Status = 'Pending'
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_DateTime DESC";
                    
            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="order">
                        <div class="order-item"><?php echo htmlspecialchars($row['Order_TicketNumber']); ?></div>
                        <div class="order-item"><?php echo htmlspecialchars($row['Order_EatingOption']); ?></div>
                        <div class="order-item"><?php echo htmlspecialchars($row['Order_ID']); ?></div>
                        <div class="order-item"><?php echo $row['items']; ?></div>
                        <div class="order-item"><?php echo htmlspecialchars($row['Payment_Method']); ?></div>
                        <div class="order-item"><?php echo date('m/d/y', strtotime($row['Order_DateTime'])); ?></div>
                        <div class="order-buttons">
                            <button class="button add-button" style="margin-left:40px">Add</button>
                        </div>
                        <div class="order-item">₱<?php echo number_format($row['Order_TotalAmount'], 2); ?></div>
                        <div class="order-buttons">
                            <button class="button done-button" style="margin-left:40px" data-order-id="<?php echo $row['Order_ID']; ?>">Confirm</button>
                            <button class="button-cancel" style="margin-left:40px" data-order-id="<?php echo $row['Order_ID']; ?>">Cancel</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            ?>
            </div>

            </div>
        </div>

       

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Custom Modal -->
        <script src="Javascript-admin/admin-modal.js"></script>
        <!-- JavaScript -->
        <script src="Javascript-admin/confirm_order.js"></script>
        <script src="Javascript-admin/cancel_order.js"></script>
        <script src="Javascript-admin/search_order.js"></script>
        <script src="Javascript-admin/mobile-menu.js"></script>
       
    </body>
</html>
