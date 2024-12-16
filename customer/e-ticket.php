<?php
session_start();
require_once 'database_customer.php';

// Get ticket number from session
$ticketNumber = isset($_SESSION['ticket_number']) ? $_SESSION['ticket_number'] : null;

// Get order details if ticket number exists
$orderDetails = null;
if ($ticketNumber) {
    $sql = "SELECT o.Order_Status, o.Order_DateTime, o.Order_TotalAmount, o.Order_EatingOption, p.Payment_Method 
            FROM `order` o 
            JOIN payment p ON o.Payment_ID = p.Payment_ID 
            WHERE o.Order_TicketNumber = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticketNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $orderDetails = $result->fetch_assoc();
    }
}

// Redirect if no ticket number
if (!$ticketNumber) {
    header("Location: menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - SINCO CAFE</title>
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

        .ticket-container {
            text-align: center;
            width: 100%;
            max-width: 300px;
            padding: 0 20px;
        }

        .ticket-title {
            font-size: 24px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .ticket-frame {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 15px;
            position: relative;
            margin: 20px 0;
        }

        /* Create ticket edges */
        .ticket-frame::before,
        .ticket-frame::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 20px;
            background-image: radial-gradient(circle at 10px -5px, transparent 12px, black 13px);
            background-size: 20px 20px;
            background-position: center;
            background-repeat: repeat-x;
        }

        .ticket-frame::before {
            top: -10px;
            transform: rotate(180deg);
        }

        .ticket-frame::after {
            bottom: -10px;
        }

        .ticket-number {
            font-size: 48px;
            font-weight: bold;
            margin: 20px 0;
        }

        .ticket-instructions {
            font-size: 14px;
            line-height: 1.4;
            margin: 15px 0;
        }

        .ticket-label {
            font-weight: bold;
            margin-top: 10px;
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            max-width: 300px;
            padding: 0 20px;
            margin-top: 20px;
        }

        .btn {
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
            font-size: 16px;
        }

        .btn-status {
            background-color: white;
            color: black;
        }

        .btn-next {
            background-color: #28a745;
            color: white;
        }

        .btn-status:hover {
            background-color: #f0f0f0;
        }

        .btn-next:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
        <p class="tagline">Where Good Coffee Starts</p>
    </div>

    <div class="ticket-container">
        <div class="ticket-title">
            Your order<br>
            E-ticket is
        </div>
        
        <div class="ticket-frame">
            <div class="ticket-number"><?php echo $ticketNumber; ?></div>
            <div class="ticket-instructions">
                Show this to the<br>
                barista at the<br>
                counter to process<br>
                your order, Thank<br>
                you!
            </div>
            <div class="ticket-label">E-TICKET</div>
        </div>
    </div>

    <div class="action-buttons">
        <button class="btn btn-status" onclick="window.location.href='orderstatusboard.php'">View Order Status</button>
        <button class="btn btn-next" onclick="window.location.href='order_status_board.php'">Next</button>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
