<?php
require_once 'database_customer.php';

// Get orders from database
$preparingOrders = [];
$claimOrders = [];

$sql = "SELECT Order_TicketNumber, Order_Status FROM `order` 
        WHERE Order_Status IN ('Pending', 'Completed') 
        ORDER BY Order_DateTime DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['Order_Status'] === 'Pending') {
            $preparingOrders[] = $row['Order_TicketNumber'];
        } else if ($row['Order_Status'] === 'Completed') {
            $claimOrders[] = $row['Order_TicketNumber'];
        }
    }
}
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
            padding: 20px;
            text-align: center;
            color: black;
            font-size: 2rem;
            font-weight: bold;
            flex-grow: 1;
        }

        .ticket-number {
            margin: 15px 0;
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
                    <div class="empty-message">No orders in preparation</div>
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
                    <div class="empty-message">No orders ready for claim</div>
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
    <script>
        // Auto refresh every 30 seconds
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
