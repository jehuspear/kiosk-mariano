<?php
session_start();
require_once 'database_customer.php';

// Get ticket number from session
$ticketNumber = isset($_SESSION['ticket_number']) ? $_SESSION['ticket_number'] : null;

// Get order details if ticket number exists
$orderDetails = null;
if ($ticketNumber) {
    // Set timezone to Philippines
    date_default_timezone_set('Asia/Manila');

    // Get today's date range in Manila time
    $today_start = date('Y-m-d 00:00:00');
    $today_end = date('Y-m-d 23:59:59');
    
    $sql = "SELECT o.*, p.Payment_Method, p.Payment_Status, p.Payment_DiscountType,
            p.Payment_DiscountAmount, p.Payment_ReferenceNumber, p.Payment_CashPaid,
            p.Payment_Change, p.Payment_TotalAmount,
            GROUP_CONCAT(
                CONCAT(
                    m.MenuItem_Name, '|',
                    oi.OrderItem_CupSize, '|',
                    oi.OrderItem_Quantity, '|',
                    oi.OrderItem_Price, '|',
                    oi.OrderItem_ID, '|',
                    ms.MenuItemSize_IsHot
                ) ORDER BY oi.OrderItem_ID ASC SEPARATOR '||'
            ) as items
            FROM `order` o 
            JOIN payment p ON o.Payment_ID = p.Payment_ID 
            JOIN orderitem oi ON o.Order_ID = oi.Order_ID
            JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
            JOIN menuitem_sizes ms ON ms.MenuItemSize_ID = oi.MenuItemSize_ID AND oi.OrderItem_CupSize = ms.MenuItemSize_SizeName
            WHERE o.Order_TicketNumber = ? 
            AND o.Order_DateTime BETWEEN ? AND ?
            GROUP BY o.Order_ID";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $ticketNumber, $today_start, $today_end);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $orderDetails = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status - White House Cafe</title>

    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="resources/favicon/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="resources/favicon/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="resources/favicon/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="resources/favicon/favicon_io/site.webmanifest">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom Modal CSS -->
    <link rel="stylesheet" href="css/order-status-modal.css">
    <!-- Temperature Badge CSS -->
    <link rel="stylesheet" href="css/order-status-temperature.css">
    <!-- Feedback Button CSS -->
    <link rel="stylesheet" href="css/feedback-button.css">
    <style>
        body {
            background-color: black;
            color: white;
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .logo-image {
            width: 200px;
            height: auto;
        }

        .tagline {
            margin-top: 10px;
            font-size: 14px;
        }

        .status-container {
            background-color: white;
            color: black;
            border-radius: 15px;
            padding: 20px;
            margin: 20px;
            width: 90%;
            max-width: 450px;
            position: relative;
        }

        .status-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .ticket-number {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }

        .status-details {
            margin: 15px 0;
        }

        .status-label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
            font-size: 0.9em;
        }

        .status-value {
            color: #333;
            margin-bottom: 15px;
            font-size: 1em;
        }

        /* Payment method badge styles */
        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
            color: white;
        }

        .payment-badge.gcash {
            background: linear-gradient(45deg, #0066FF, #00A4FF);
        }

        .payment-badge.cash {
            background: linear-gradient(45deg, #28a745, #34ce57);
        }

        /* Item styles */
        .status-value ul li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .item-info {
            flex: 1;
            padding-right: 10px;
        }

        .item-price {
            white-space: nowrap;
            font-weight: 500;
            color: #444;
        }

        /* Payment details section */
        .payment-details-section {
            margin-top: 20px;
            padding: 15px;
            border-top: 2px solid #eee;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 4px 0;
        }

        .payment-label {
            color: #666;
            font-weight: 600;
            font-size: 0.9em;
        }

        .payment-value {
            color: #333;
            font-weight: 500;
            text-align: right;
        }

        .payment-row.total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #ddd;
        }

        .payment-row.total .payment-label {
            font-size: 1em;
            color: #333;
        }

        .payment-row.total .payment-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #28a745;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            margin-top: 10px;
        }

        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        .status-preparing {
            background-color:rgb(255, 251, 7);
            color: black;
        }

        .status-readytoclaim {
            background-color: #28a745;
            color: white;
        }
        .status-completed {
            background-color: #28a745;
            color: white;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: white;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
            width: 90%;
            max-width: 400px;
        }

        .btn-back {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #218838;
            color: white;
            text-decoration: none;
        }

        .no-ticket {
            text-align: center;
            margin: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        .refresh-button {
            background: none;
            border: none;
            color: #28a745;
            cursor: pointer;
            font-size: 20px;
            position: absolute;
            top: 20px;
            right: 20px;
            transition: color 0.2s;
        }

        .refresh-button:hover {
            color: #218838;
        }

        .btn-received {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin: 15px auto;
            display: block;
            width: 100%;
        }

        .btn-received:hover {
            background-color: #218838;
        }

        @media (max-width: 576px) {
            .payment-badge {
                font-size: 0.85em;
                padding: 3px 10px;
            }
            
            .payment-row.total .payment-value {
                font-size: 1.1em;
            }

            .payment-label {
                font-size: 0.85em;
            }

            .payment-value {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <!-- Audio element for notification -->
    <audio id="notificationSound" preload="auto">
        <source src="resources/notification/notification.mp3" type="audio/mpeg">
    </audio>

    <div class="logo-container">
        <img src="resources/images/logo.png" alt="CAFE Logo" class="logo-image">
        <!-- <p class="tagline">Where Good Coffee Starts</p> -->
    </div>

    <?php if ($orderDetails): ?>
        <div class="status-container">
            <button onclick="location.reload()" class="refresh-button" title="Refresh Status">
                <i class="fas fa-sync-alt"></i>
            </button>
            
            <div class="status-header">
                <div class="ticket-number">Ticket #<?php echo $ticketNumber; ?></div>
                <div class="status-badge status-<?php echo strtolower($orderDetails['Order_Status']); ?>">
                    <?php echo $orderDetails['Order_Status']; ?>
                </div>
            </div>

            <div class="status-details">
                <div class="status-label">Order ID</div>
                <div class="status-value">
                    <?php echo str_pad($orderDetails['Order_ID'], 8, '0', STR_PAD_LEFT); ?>
                </div>

                <div class="status-label">Order Time</div>
                <div class="status-value">
                    <?php echo date('M d, Y h:i A', strtotime($orderDetails['Order_DateTime'])); ?>
                </div>

                <div class="status-label">Order Type</div>
                <div class="status-value"><?php echo $orderDetails['Order_EatingOption']; ?></div>

                <div class="status-label">Payment Method</div>
                <div class="status-value">
                    <span class="payment-badge <?php echo strtolower($orderDetails['Payment_Method']); ?>">
                        <?php 
                        $icon = $orderDetails['Payment_Method'] === 'GCash' ? 'fa-mobile-alt' : 'fa-money-bill-wave';
                        echo "<i class='fas {$icon}'></i> " . $orderDetails['Payment_Method']; 
                        ?>
                    </span>
                </div>

                <div class="status-label">Items</div>
                <div class="status-value">
                    <?php 
                    $itemsList = explode('||', $orderDetails['items']);
                    echo "<ul style='list-style-type: none; padding-left: 0; margin: 0;'>";
                    foreach ($itemsList as $item) {
                        list($name, $size, $quantity, $price, $itemId, $temperature) = explode('|', $item);
                        $total = $price * $quantity;
                        
                        // Determine temperature class and icon
                        $tempClass = 'normal';
                        $tempIcon = 'fa-thermometer-half';
                        $tempText = 'Normal';
                        
                        switch($temperature) {
                            case 'Hot':
                                $tempClass = 'hot';
                                $tempIcon = 'fa-fire';
                                $tempText = 'Hot';
                                break;
                            case 'Iced':
                                $tempClass = 'iced';
                                $tempIcon = 'fa-snowflake';
                                $tempText = 'Iced';
                                break;
                        }
                        
                        echo "<li>";
                        echo "<div class='item-info'>";
                        echo "$name ($size <span class='temp-badge {$tempClass}'><i class='fas {$tempIcon}'></i>{$tempText}</span>) ";
                        echo "x$quantity";
                        echo "</div>";
                        echo "<div class='item-price'>₱" . number_format($total, 2) . "</div>";
                        echo "</li>";
                    }
                    echo "</ul>";
                    ?>
                </div>

                <?php if (in_array($orderDetails['Order_Status'], ['Preparing', 'ReadyToClaim', 'Completed'])): ?>
                    <div class="payment-details-section">
                        <!-- Subtotal -->
                        <div class="payment-row">
                            <div class="payment-label">Sub Total</div>
                            <div class="payment-value">₱<?php echo number_format($orderDetails['Order_TotalAmount'], 2); ?></div>
                        </div>

                        <!-- Discount Information -->
                        <?php if ($orderDetails['Payment_DiscountType']): ?>
                            <div class="payment-row">
                                <div class="payment-label">Discount Type</div>
                                <div class="payment-value"><?php echo $orderDetails['Payment_DiscountType']; ?></div>
                            </div>
                            <div class="payment-row">
                                <div class="payment-label">Discount Amount</div>
                                <div class="payment-value">₱<?php echo number_format($orderDetails['Payment_DiscountAmount'], 2); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Payment Details -->
                        <?php if ($orderDetails['Payment_Method'] === 'GCash'): ?>
                            <div class="payment-row">
                                <div class="payment-label">Reference Number</div>
                                <div class="payment-value"><?php echo $orderDetails['Payment_ReferenceNumber']; ?></div>
                            </div>
                        <?php else: ?>
                            <div class="payment-row">
                                <div class="payment-label">Cash Paid</div>
                                <div class="payment-value">₱<?php echo number_format($orderDetails['Payment_CashPaid'], 2); ?></div>
                            </div>
                            <div class="payment-row">
                                <div class="payment-label">Change</div>
                                <div class="payment-value">₱<?php echo number_format($orderDetails['Payment_Change'], 2); ?></div>
                            </div>
                        <?php endif; ?>

                        
                    </div>
                <?php endif; ?>
                <!-- Total Amount -->
                <div class="payment-row total">
                            <div class="payment-label">Total Amount</div>
                            <div class="payment-value">₱<?php echo number_format($orderDetails['Payment_TotalAmount'], 2); ?></div>
                        </div>
            </div>

            <?php 
            // Check for existing feedback if order is completed
            $hasFeedback = false;
            if ($orderDetails['Order_Status'] === 'Completed') {
                $feedbackCheck = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE Order_ID = ?");
                $feedbackCheck->bind_param("i", $orderDetails['Order_ID']);
                $feedbackCheck->execute();
                $feedbackResult = $feedbackCheck->get_result();
                $hasFeedback = $feedbackResult->fetch_assoc()['count'] > 0;
                $feedbackCheck->close();
            }
            ?>

            <?php if ($orderDetails['Order_Status'] === 'ReadyToClaim'): ?>
                <button id="orderReceivedBtn" class="btn-received">
                    Click Here to Confirm Order Received
                </button>
            <?php elseif ($orderDetails['Order_Status'] === 'Completed'): ?>
                <?php if ($hasFeedback): ?>
                    <div style="text-align: center; padding: 15px; margin: 15px 0; background: linear-gradient(45deg, #28a745, #34ce57); border-radius: 10px; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p style="margin: 0; font-weight: bold;">Feedback Submitted</p>
                        <p style="margin: 5px 0 0 0; font-size: 0.9em;">Thank you for helping us improve our service!</p>
                    </div>
                <?php else: ?>
                    <a href="customerfeedback.php" class="btn-feedback animate">
                        <i class="fas fa-star"></i> Submit Feedback Here
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
    <?php else: ?>
        <div class="no-ticket">
            <h3>No ticket found</h3>
            <p>Please make sure you have a valid ticket number.</p>
        </div>
    <?php endif; ?>

    <div class="action-buttons">
        <a href="order-status-board.php" target="_blank" class="btn-back">View Order Status Board</a>
    </div>
    <div class="action-buttons">
        <a href="e-ticket.php" class="btn-back">Back to your E-Ticket Number</a>
    </div>
    <div class="action-buttons">
        <a href="menu.php" class="btn-back">Back to Menu</a>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Modal JS -->
    <script src="javascript/order-status-modal.js"></script>
    <script>
        // Function to fetch and update order status
        // Function to play notification sound
        function playNotification() {
            const audio = document.getElementById('notificationSound');
            if (audio) {
                audio.play().catch(error => {
                    console.log('Error playing notification:', error);
                });
            }
        }

        async function updateOrderStatus() {
            const ticketNumber = <?php echo json_encode($ticketNumber); ?>;
            if (!ticketNumber) return;

            try {
                const response = await fetch('get_order_status.php?ticket_number=' + ticketNumber);
                const data = await response.json();
                
                if (data.success) {
                    const statusBadge = document.querySelector('.status-badge');
                    const currentStatus = statusBadge ? statusBadge.textContent : '';
                    
                    // Update status badge
                    if (statusBadge) {
                        statusBadge.className = 'status-badge status-' + data.status.toLowerCase();
                        statusBadge.textContent = data.status;
                        
                        // Play notification sound only when status changes to ReadyToClaim
                        if (data.status === 'ReadyToClaim' && currentStatus !== 'ReadyToClaim') {
                            playNotification();
                        }
                    }

                    // Check if we need to reload the page to show/hide the Order Received button
                    const currentHasButton = document.getElementById('orderReceivedBtn') !== null;
                    const shouldHaveButton = data.status === 'ReadyToClaim';
                    
                    if (currentHasButton !== shouldHaveButton) {
                        location.reload();
                        return;
                    }

                    // Stop auto-refresh if status is not Pending, Preparing, or ReadyToClaim
                    const refreshableStatuses = ['Pending', 'Preparing', 'ReadyToClaim'];
                    if (!refreshableStatuses.includes(data.status)) {
                        clearInterval(refreshInterval);
                    }
                }
            } catch (error) {
                console.error('Error fetching order status:', error);
            }
        }

        // Set up auto-refresh interval only if there's a ticket number and initial status is refreshable
        let refreshInterval;
        const initialStatus = <?php echo json_encode($orderDetails ? $orderDetails['Order_Status'] : null); ?>;
        const refreshableStatuses = ['Pending', 'Preparing', 'ReadyToClaim'];
        
        if (<?php echo json_encode($ticketNumber !== null); ?> && 
            initialStatus && 
            refreshableStatuses.includes(initialStatus)) {
            refreshInterval = setInterval(updateOrderStatus, 3000);
            
            // Initial update
            updateOrderStatus();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const orderReceivedBtn = document.getElementById('orderReceivedBtn');
            if (orderReceivedBtn) {
                orderReceivedBtn.addEventListener('click', async function() {
                    const confirmed = await modal.confirm(
                        'Confirm Order Receipt',
                        '<div class="text-center mb-3"><i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i></div>Have you received your order?'
                    );
                    
                    if (confirmed) {
                        const ticketNumber = <?php echo json_encode($ticketNumber); ?>;
                        
                        try {
                            const response = await fetch('update_order_status.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'ticket_number=' + ticketNumber + '&status=Completed'
                            });
                            
                            const data = await response.json();
                            
                            if (data.success) {
                                await modal.alert(
                                    'Success',
                                    'Your order has been marked as completed.',
                                    'success'
                                );
                                location.reload();
                            } else {
                                await modal.alert(
                                    'Error',
                                    'Failed to update order status. Please try again.',
                                    'error'
                                );
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            await modal.alert(
                                'Error',
                                'An error occurred. Please try again.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
