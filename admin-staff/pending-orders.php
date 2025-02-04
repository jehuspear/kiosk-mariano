<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        <link rel="stylesheet" href="Css-admin/modal-order-details.css">
        <link rel="stylesheet" href="Css-admin/receipt-print.css">
       
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

                <!-- Column Headers -->
                <div class="navbar">
                    <div class="navbar-item">Order Ticket No</div>
                    <div class="navbar-item">Date</div>
                    <div class="navbar-item">Eating Option</div>
                    <div class="navbar-item">Payment Method</div>
                    <div class="navbar-item">Order Item</div>
                    <div class="navbar-item">Product Item Price</div>
                    <div class="navbar-item">Total</div>
                    <div class="navbar-item">Actions</div>
                </div>

                <!-- Order Details -->
                <div class="order-details-container">
                <?php
                require_once 'database_admin.php';
                
                // Set timezone to Philippines
                date_default_timezone_set('Asia/Manila');

                // Get today's date range in Manila time
                $today_start = date('Y-m-d 00:00:00');
                $today_end = date('Y-m-d 23:59:59');
                
                $sql = "SELECT 
                        o.*,
                        GROUP_CONCAT(
                            CONCAT(oi.OrderItem_Quantity, ' x ', m.MenuItem_Name, ' (', oi.OrderItem_CupSize, ')')
                            ORDER BY oi.OrderItem_ID ASC
                            SEPARATOR '<br>'
                        ) as items,
                        GROUP_CONCAT(
                            CONCAT('₱', FORMAT(ms.MenuItemSize_Price * oi.OrderItem_Quantity, 2))
                            ORDER BY oi.OrderItem_ID ASC
                            SEPARATOR '<br>'
                        ) as item_prices
                        FROM `order` o
                        JOIN orderitem oi ON o.Order_ID = oi.Order_ID
                        JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
                        JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID AND oi.OrderItem_CupSize = ms.MenuItemSize_SizeName
                        WHERE o.Order_Status = 'Pending'
                        AND o.Order_DateTime BETWEEN ? AND ?
                        GROUP BY o.Order_ID
                        ORDER BY o.Order_DateTime DESC";

                // Prepare and execute the statement
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    error_log("Failed to prepare statement: " . mysqli_error($conn));
                    die("Database error occurred");
                }

                mysqli_stmt_bind_param($stmt, "ss", $today_start, $today_end);
                if (!mysqli_stmt_execute($stmt)) {
                    error_log("Failed to execute statement: " . mysqli_error($conn));
                    die("Database error occurred");
                }

                $result = mysqli_stmt_get_result($stmt);
                        
                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="order">
                            <div class="order-item"><?php echo str_pad(htmlspecialchars($row['Order_TicketNumber']), 3, '0', STR_PAD_LEFT); ?></div>
                            <div class="order-item"><?php echo date('m/d/y h:i A', strtotime($row['Order_DateTime'])); ?></div>
                            <div class="order-item"><?php echo htmlspecialchars($row['Order_EatingOption']); ?></div>
                            <div class="order-item"><?php echo htmlspecialchars($row['Payment_Method']); ?></div>
                            <div class="order-item"><?php echo $row['items']; ?></div>
                            <div class="order-item"><?php echo $row['item_prices']; ?></div>
                            <div class="order-item">₱<?php echo number_format($row['Order_TotalAmount'], 2); ?></div>
                            <div class="order-buttons">
                                <button class="button done-button" data-order-id="<?php echo $row['Order_ID']; ?>" title="Confirm Order">
                                    <span>Confirm Payment</span>
                                </button>
                                <button class="button-cancel" data-order-id="<?php echo $row['Order_ID']; ?>" title="Cancel Order">
                                    <span>Cancel Order</span>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '
                    <div class="no-orders-container">
                        <div class="no-orders-content">
                            <i class="fas fa-clipboard-check no-orders-icon"></i>
                            <h3>No Pending Orders</h3>
                            <p>There are no pending orders at the moment.</p>
                            <p class="refresh-note">The page will automatically refresh when new orders arrive.</p>
                        </div>
                    </div>';
                }
                ?>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom Scripts - Order matters! -->
        <script src="Javascript-admin/receipt-printer.js"></script>
        <script src="Javascript-admin/admin-modal.js"></script>
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
        
        <style>
            /* Grid Layout Styles */
            .navbar {
                display: grid;
                grid-template-columns: 0.7fr 1fr 0.8fr 0.8fr 1.5fr 1fr 0.7fr 1fr;
                gap: 8px;
                padding: 12px;
                background-color: #333;
                color: white;
                font-weight: bold;
                align-items: center;
                text-align: center;
                font-size: 0.9rem;
            }

            .order {
                display: grid;
                grid-template-columns: 0.7fr 1fr 0.8fr 0.8fr 1.5fr 1fr 0.7fr 1fr;
                gap: 8px;
                padding: 12px;
                align-items: start;
                border-bottom: 1px solid #ddd;
                background-color: rgb(41, 42, 43);
                min-height: 60px;
            }

            .order:hover {
                background-color: rgb(48, 51, 54);
            }

            .order-item {
                padding: 8px;
                text-align: center;
                overflow: hidden;
                text-overflow: ellipsis;
                word-wrap: break-word;
                color: #fff;
                font-size: 0.9rem;
                line-height: 1.4;
            }

            .order-buttons {
                display: flex;
                gap: 8px;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }

            .button,
            .button-cancel {
                width: 100%;
                padding: 8px;
                border-radius: 4px;
                border: none;
                cursor: pointer;
                font-size: 0.85rem;
                transition: all 0.2s ease;
                text-align: center;
                display: inline-block;
                line-height: 1.2;
                margin: 0;
            }

            .done-button {
                background-color: #28a745;
                color: white;
            }

            .button-cancel {
                background-color: #dc3545;
                color: white;
            }

            .button:hover,
            .button-cancel:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }

            .button:active,
            .button-cancel:active {
                transform: translateY(1px);
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

            /* No Orders Styling */
            .no-orders-container {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 300px;
                width: 100%;
                background-color: rgb(41, 42, 43);
                border-radius: 10px;
                margin-top: 20px;
            }

            .no-orders-content {
                text-align: center;
                padding: 30px;
                color: #fff;
            }

            .no-orders-icon {
                font-size: 4rem;
                color: #28a745;
                margin-bottom: 20px;
            }

            .no-orders-content h3 {
                font-size: 1.5rem;
                margin-bottom: 10px;
                color: #fff;
            }

            .no-orders-content p {
                color: #aaa;
                margin-bottom: 5px;
            }

            .refresh-note {
                font-size: 0.9rem;
                color: #666 !important;
                margin-top: 15px;
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                .navbar,
                .order {
                    grid-template-columns: 1fr 1fr 1fr 1fr;
                }

                .navbar-item:nth-child(n+5),
                .order-item:nth-child(n+5) {
                    display: none;
                }

                .order-buttons {
                    grid-column: span 4;
                    justify-content: center;
                    margin-top: 10px;
                    flex-direction: row;
                }

                .button,
                .button-cancel {
                    padding: 6px 12px;
                    font-size: 12px;
                    width: auto;
                }
            }
        </style>

        <!-- Refresh Indicator -->
        <div class="refresh-indicator">
            <i class="fas fa-sync-alt"></i> Refreshing...
        </div>
    </body>
</html>
