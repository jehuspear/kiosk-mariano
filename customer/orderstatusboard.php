<?php
session_start();
require_once 'database_customer.php';

// Get ticket number from session
$ticketNumber = isset($_SESSION['ticket_number']) ? $_SESSION['ticket_number'] : null;

// Get order details if ticket number exists
$orderDetails = null;
if ($ticketNumber) {
    $sql = "SELECT o.*, p.Payment_Method, p.Payment_Status, 
            GROUP_CONCAT(CONCAT(m.MenuItem_Name, ' (', oi.OrderItem_CupSize, ') x', oi.OrderItem_Quantity) SEPARATOR ', ') as items
            FROM `order` o 
            JOIN payment p ON o.Payment_ID = p.Payment_ID 
            JOIN orderitem oi ON o.Order_ID = oi.Order_ID
            JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
            WHERE o.Order_TicketNumber = ?
            GROUP BY o.Order_ID";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticketNumber);
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
    <title>Order Status - SINCO CAFE</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            max-width: 400px;
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
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
        <p class="tagline">Where Good Coffee Starts</p>
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
                <div class="status-label">Order Time</div>
                <div class="status-value">
                    <?php echo date('M d, Y h:i A', strtotime($orderDetails['Order_DateTime'])); ?>
                </div>

                <div class="status-label">Order Type</div>
                <div class="status-value"><?php echo $orderDetails['Order_EatingOption']; ?></div>

                <div class="status-label">Payment Method</div>
                <div class="status-value"><?php echo $orderDetails['Payment_Method']; ?></div>

                <div class="status-label">Items</div>
                <div class="status-value"><?php echo $orderDetails['items']; ?></div>

                <div class="status-label">Total Amount</div>
                <div class="status-value">₱<?php echo number_format($orderDetails['Order_TotalAmount'], 2); ?></div>
            </div>
        </div>
    <?php else: ?>
        <div class="no-ticket">
            <h3>No ticket found</h3>
            <p>Please make sure you have a valid ticket number.</p>
        </div>
    <?php endif; ?>

    <div class="action-buttons">
        <a href="menu.php" class="btn-back">Back to Menu</a>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
