<?php
require_once 'database_customer.php';

// Get orders from database
$preparingOrders = [];
$claimOrders = [];

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get today's date range in Manila time
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

// Debug information
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug output in HTML comments
echo "<!-- Debug Info:\n";
echo "Today's date range: " . $today_start . " to " . $today_end . "\n";

// Get preparing orders
$preparingSql = "SELECT Order_TicketNumber, Order_DateTime FROM `order` 
                WHERE Order_Status = 'Preparing' 
                AND Order_DateTime BETWEEN ? AND ?
                ORDER BY Order_DateTime ASC";
$stmt = $conn->prepare($preparingSql);
$stmt->bind_param("ss", $today_start, $today_end);
$stmt->execute();
$result = $stmt->get_result();

// echo "\nPreparing Orders Query:\n" . $preparingSql . "\n";
// echo "Preparing Orders Found: " . $result->num_rows . "\n";

while ($row = $result->fetch_assoc()) {
    $formattedNumber = str_pad($row['Order_TicketNumber'], 3, '0', STR_PAD_LEFT);
    $preparingOrders[] = $formattedNumber;
    echo "Found Preparing order: " . $formattedNumber . " from " . $row['Order_DateTime'] . "\n";
}

// Get ready to claim orders
$claimSql = "SELECT Order_TicketNumber, Order_DateTime FROM `order` 
             WHERE Order_Status = 'ReadyToClaim' 
             AND Order_DateTime BETWEEN ? AND ?
             ORDER BY Order_DateTime ASC";
$stmt = $conn->prepare($claimSql);
$stmt->bind_param("ss", $today_start, $today_end);
$stmt->execute();
$result = $stmt->get_result();

// echo "\nClaim Orders Query:\n" . $claimSql . "\n";
// echo "Claim Orders Found: " . $result->num_rows . "\n";

while ($row = $result->fetch_assoc()) {
    $formattedNumber = str_pad($row['Order_TicketNumber'], 3, '0', STR_PAD_LEFT);
    $claimOrders[] = $formattedNumber;
    echo "Found ReadyToClaim order: " . $formattedNumber . " from " . $row['Order_DateTime'] . "\n";
}

echo " -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Board - SINCO CAFE</title>
    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/order-status-board.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
        <p>Where Good Coffee Starts</p>
    </header>

    <!-- Refresh Indicator -->
    <div class="refresh-indicator">
        <i class="fas fa-sync-alt"></i>
    </div>

    <!-- Order Board -->
    <div class="order-board">
        <div class="column preparing">
            <h2>Preparing...</h2>
            <div class="ticket-numbers">
                <?php if (empty($preparingOrders)): ?>
                    <!-- <div class="empty-message">No orders in preparation</div> -->
                <?php else: ?>
                    <?php foreach ($preparingOrders as $ticketNumber): ?>
                        <div class="ticket-number"><?php echo $ticketNumber; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="column claim">
            <h2>Please Claim</h2>
            <div class="ticket-numbers">
                <?php if (empty($claimOrders)): ?>
                    <!-- <div class="empty-message">No orders ready for claim</div> -->
                <?php else: ?>
                    <?php foreach ($claimOrders as $ticketNumber): ?>
                        <div class="ticket-number"><?php echo $ticketNumber; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <img src="resources/images/cafe.png" alt="cafe" class="footer-logo">
            <p class="footer-text">Thank you for Purchasing!</p>
            <img src="resources/images/coffeebeans.png" alt="Coffee Bean" class="footer-logo">
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Auto Complete Orders -->
    <script src="javascript/auto-complete-orders.js"></script>
    <script>
        // Auto refresh every 5 seconds
        setInterval(function() {
            const refreshIndicator = document.querySelector('.refresh-indicator');
            refreshIndicator.style.display = 'block';
            
            setTimeout(function() {
                location.reload();
            }, 1000);
        }, 5000);
    </script>
</body>
</html>
