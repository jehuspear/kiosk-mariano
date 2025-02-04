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

echo "\nPreparing Orders Query:\n" . $preparingSql . "\n";
echo "Preparing Orders Found: " . $result->num_rows . "\n";

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

echo "\nClaim Orders Query:\n" . $claimSql . "\n";
echo "Claim Orders Found: " . $result->num_rows . "\n";

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
    <style>
        body {
            background-color: black;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        .logo-image {
            width: 266px;
            height: auto;
            margin-top: 30px;
        }

        .header {
            background-color: black;
            padding: 20px 0;
            text-align: center;
        }

        .order-board {
            display: flex;
            margin: 20px auto;
            width: 90%;
            max-width: 450px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .column {
            flex: 1;
            min-height: 400px;
            display: flex;
            flex-direction: column;
        }

        .column:first-child {
            border-right: 1px solid #ddd;
        }

        .column h2 {
            margin: 0;
            padding: 15px;
            text-align: center;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .preparing h2 {
            background-color: #dc3545;
        }

        .claim h2 {
            background-color: #28a745;
        }

        .ticket-numbers {
            padding: 15px;
            color: black;
            font-size: 2.5rem;
            font-weight: bold;
            flex-grow: 1;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            align-content: start;
        }

        .preparing .ticket-number {
            text-align: center;
            padding: 10px;
            border: 2px solid rgba(220, 53, 69, 0.3);  /* Light red from #dc3545 */
            border-radius: 8px;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .claim .ticket-number {
            text-align: center;
            padding: 10px;
            border: 2px solid rgba(40, 167, 69, 0.3);  /* Light green from #28a745 */
            border-radius: 8px;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .empty-message {
            grid-column: 1 / -1;
            text-align: center;
            color: #666;
            padding: 20px;
            font-style: italic;
        }

        .footer {
            background-color: black;
            padding: 15px 0;
            margin-top: auto;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .footer-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .footer-logo {
            width: 30px;
            height: auto;
        }

        .footer-text {
            margin: 0;
            font-size: 16px;
            color: white;
        }

        /* Auto-refresh indicator */
        .refresh-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 20px;
            animation: spin 2s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Empty state message */
        .empty-message {
            text-align: center;
            color: #666;
            padding: 20px;
            font-style: italic;
        }
    </style>
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
