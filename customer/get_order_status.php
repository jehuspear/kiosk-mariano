<?php
require_once 'database_customer.php';

// Get ticket number from query parameter
$ticketNumber = isset($_GET['ticket_number']) ? $_GET['ticket_number'] : null;

if (!$ticketNumber) {
    echo json_encode([
        'success' => false,
        'message' => 'Ticket number is required'
    ]);
    exit;
}

// Get today's date in Y-m-d format
$today = date('Y-m-d');

// Get order status from database for today's orders only
$sql = "SELECT Order_Status FROM `order` WHERE Order_TicketNumber = ? AND DATE(Order_DateTime) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $ticketNumber, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'status' => $order['Order_Status']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Order not found'
    ]);
}
