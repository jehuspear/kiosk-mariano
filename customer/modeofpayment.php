<?php
session_start();
require_once 'database_customer.php';

// Handle payment method selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];
    $totalAmount = 0;
    $eatingOption = '';
    
    // Calculate total amount and get eating option from cart
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
            $eatingOption = $item['orderType']; // Get from first item
        }
        
        // Get today's date in Y-m-d format for comparison
        $today = date('Y-m-d');
        
        // Get the latest ticket number from today's orders
        $sql = "SELECT Order_TicketNumber FROM `order` 
                WHERE DATE(Order_DateTime) = ? 
                ORDER BY Order_DateTime DESC, Order_ID DESC 
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If there are no orders today, start from 1
        // Otherwise, increment the last ticket number
        if ($result->num_rows === 0) {
            $ticketNumber = 1;
        } else {
            $row = $result->fetch_assoc();
            $ticketNumber = $row['Order_TicketNumber'] + 1;
        }
        
        // Format ticket number to have leading zeros (e.g., 001, 012, 123)
        $formattedTicketNumber = str_pad($ticketNumber, 3, '0', STR_PAD_LEFT);
        
        $_SESSION['ticket_number'] = $formattedTicketNumber;
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert payment record
            $sql = "INSERT INTO payment (Payment_Method, Payment_DateTime, Order_TotalAmount, Payment_TotalAmount, Payment_Status) 
                    VALUES (?, NOW(), ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdd", $paymentMethod, $totalAmount, $totalAmount);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert payment record");
            }
            $paymentId = $conn->insert_id;
            
            // Insert order record with the generated ticket number
            $sql = "INSERT INTO `order` (Order_EatingOption, Order_TicketNumber, Order_DateTime, Order_TotalAmount, Payment_ID, Payment_Method, Order_Status) 
                    VALUES (?, ?, NOW(), ?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sidis", $eatingOption, $ticketNumber, $totalAmount, $paymentId, $paymentMethod);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert order record");
            }
            $orderId = $conn->insert_id;
            
            // Insert order items
            $sql = "INSERT INTO orderitem (Order_ID, MenuItem_ID, OrderItem_CupSize, OrderItem_Quantity, OrderItem_Price) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            foreach ($_SESSION['cart'] as $item) {
                $stmt->bind_param("iisid", $orderId, $item['id'], $item['size'], $item['quantity'], $item['price']);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert order items");
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart after successful order
            unset($_SESSION['cart']);
            
            // Redirect to e-ticket page
            header("Location: e-ticket.php");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method - SINCO CAFE</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: black;
            color: white;
            text-align: center;
            font-family: sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .payment-button {
            display: block;
            margin: 0 auto;
            width: 200px;
            height: 150px;
            border-radius: 10px;
            background-color: white;
            margin-bottom: 20px;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .logo-image {
            width: 266px;
            height: auto;
            margin-top: 30px;
        }

        .payment-button img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .payment-button:hover {
            transform: scale(1.05);
            background-color: #f0f0f0;
        }

        .payment-label {
            color: white;
            margin-top: 10px;
            font-size: 1.2em;
        }

        .error-message {
            color: #dc3545;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-auto">
                    <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <h3 class="mb-4">Choose your payment method</h3>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="paymentForm">
            <input type="hidden" name="payment_method" id="paymentMethod">
            
            <!-- Cash Payment Option -->
            <button type="button" class="payment-button" onclick="submitPayment('Cash')">
                <img src="resources/images/cash.png" alt="Cash">
            </button>
            <p class="payment-label">Cash</p>
            
            <!-- GCash Payment Option -->
            <button type="button" class="payment-button" onclick="submitPayment('GCash')">
                <img src="resources/images/gcash.png" alt="GCash">
            </button>
            <p class="payment-label">GCash</p>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function submitPayment(method) {
            document.getElementById('paymentMethod').value = method;
            document.getElementById('paymentForm').submit();
        }
    </script>
</body>
</html>
