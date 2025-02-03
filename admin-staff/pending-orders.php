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
        
        <style>
            /* Modal Styles */
            .admin-modal {
                max-width: 600px;
                width: 90%;
                background: white;
                border-radius: 8px;
                /* padding: 20px; */
            }

            .order-items-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .order-items-list li {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 10px;
                padding: 5px 0;
                border-bottom: 1px solid #eee;
            }

            .item-price {
                color: #666;
                font-size: 0.9em;
                margin-left: 15px;
                text-align: right;
            }

            .order-detail-item {
                margin-bottom: 15px;
            }

            .order-detail-label {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
                color: #333;
            }

            .order-detail-value {
                color: #666;
            }

            .form-control {
                width: 188px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                margin-top: 5px;
            }

            .total-section {
                margin-top: 20px;
                padding-top: 15px;
                border-top: 2px solid #eee;
            }

            .subtotal-amount,
            .discount-amount,
            .total-amount {
                font-size: 1.1em;
                font-weight: bold;
            }

            .total-amount {
                color: #28a745;
                font-size: 1.2em;
            }

            .ticket-number-display {
                font-size: 1.2em;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 4px;
            }

            /* Table Layout Styles */
            .navbar {
                display: grid;
                grid-template-columns: 0.8fr 0.8fr 1fr 1fr 1.5fr 1.5fr 0.8fr 1fr;
                gap: 10px;
                padding: 10px;
                background-color: #333;
                color: white;
                font-weight: bold;
            }

            .navbar-item {
                padding: 8px;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .order {
                display: grid;
                grid-template-columns: 0.8fr 0.8fr 1fr 1fr 1.5fr 1.5fr 0.8fr 1fr;
                gap: 10px;
                padding: 10px;
                align-items: center;
                border-bottom: 1px solid #ddd;
            }

            .order-item {
                padding: 8px;
                text-align: center;
            }

            .order-item:nth-child(5),
            .order-item:nth-child(6) {
                text-align: center;
            }

            .order-buttons {
                display: flex;
                gap: 5px;
                justify-content: center;
            }

            .button {
                padding: 5px 15px;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                font-size: 14px;
            }

            .done-button {
                background-color: #28a745;
                color: white;
            }

            .button-cancel {
                background-color: #dc3545;
                color: white;
                padding: 5px 15px;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                font-size: 14px;
            }

            /* Refresh Indicator */
            .refresh-indicator {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: rgba(40, 167, 69, 0.9);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                display: none;
                animation: fadeInOut 1s ease;
                z-index: 1000;
            }

            @keyframes fadeInOut {
                0% { opacity: 0; }
                50% { opacity: 1; }
                100% { opacity: 0; }
            }
        </style>
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

                <!-- Column Headers -->
                <div class="navbar">
                    <div class="navbar-item">Order Ticket No</div>
                    <div class="navbar-item">Date</div>
                    <div class="navbar-item">Eating Option</div>
                    <div class="navbar-item">Payment Method</div>
                    <div class="navbar-item">Order Item</div>
                    <div class="navbar-item">Product Item Price</div>
                    <div class="navbar-item">Total</div>
                    <div class="navbar-item">Status</div>
                </div>

                <!-- Order Details -->
                <div class="order-details-container">
                <?php
                require_once 'database_admin.php';
                
                // Get current date in the same format as the database
                $today = date('Y-m-d');
                
                $sql = "SELECT 
                        o.*,
                        GROUP_CONCAT(CONCAT(oi.OrderItem_Quantity, ' x ', m.MenuItem_Name, ' (', oi.OrderItem_CupSize, ')') SEPARATOR '<br>') as items,
                        GROUP_CONCAT(CONCAT('₱', FORMAT(ms.MenuItemSize_Price, 2), ' x ', oi.OrderItem_Quantity, ' = ₱', FORMAT(ms.MenuItemSize_Price * oi.OrderItem_Quantity, 2)) SEPARATOR '<br>') as item_prices
                        FROM `order` o
                        JOIN orderitem oi ON o.Order_ID = oi.Order_ID
                        JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
                        JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID AND oi.OrderItem_CupSize = ms.MenuItemSize_SizeName
                        WHERE o.Order_Status = 'Pending'
                        AND DATE(o.Order_DateTime) = ?
                        GROUP BY o.Order_ID
                        ORDER BY o.Order_DateTime DESC";

                // Prepare and execute the statement
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $today);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                        
                if ($result) {
                    // Debug logging
                    error_log("Fetching orders for date: " . $today);
                    while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="order">
                            <div class="order-item"><?php echo str_pad(htmlspecialchars($row['Order_TicketNumber']), 3, '0', STR_PAD_LEFT); ?></div>
                            <div class="order-item"><?php echo date('m/d/y', strtotime($row['Order_DateTime'])); ?></div>
                            <div class="order-item"><?php echo htmlspecialchars($row['Order_EatingOption']); ?></div>
                            <div class="order-item"><?php echo htmlspecialchars($row['Payment_Method']); ?></div>
                            <div class="order-item"><?php echo $row['items']; ?></div>
                            <div class="order-item"><?php echo $row['item_prices']; ?></div>
                            <div class="order-item">₱<?php echo number_format($row['Order_TotalAmount'], 2); ?></div>
                            <div class="order-buttons">
                                <button class="button done-button" data-order-id="<?php echo $row['Order_ID']; ?>">Confirm</button>
                                <button class="button-cancel" data-order-id="<?php echo $row['Order_ID']; ?>">Cancel</button>
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
        <script src="Javascript-admin/auto-refresh.js"></script>
        
        <script>
            // Add event listeners after all scripts are loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize confirm button handlers
                document.querySelectorAll('.done-button').forEach(button => {
                    button.addEventListener('click', window.confirmOrderHandler);
                });

                // Initialize cancel button handlers
                document.querySelectorAll('.button-cancel').forEach(button => {
                    button.addEventListener('click', window.cancelOrderHandler);
                });
            });
        </script>
        
        <!-- Add refresh indicator -->
        <style>
            .refresh-indicator {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: rgba(40, 167, 69, 0.9);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                display: none;
                animation: fadeInOut 1s ease;
                z-index: 1000;
            }
            @keyframes fadeInOut {
                0% { opacity: 0; }
                50% { opacity: 1; }
                100% { opacity: 0; }
            }
        </style>
        <div class="refresh-indicator">
            <i class="fas fa-sync-alt"></i> Refreshing...
        </div>
    </body>
</html>
