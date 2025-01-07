<?php
session_start();
require_once 'database_customer.php';

header('Content-Type: application/json');

$ticketNumber = isset($_SESSION['ticket_number']) ? $_SESSION['ticket_number'] : null;
$response = ['status' => null];

if ($ticketNumber) {
    $sql = "SELECT Order_Status FROM `order` WHERE Order_TicketNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticketNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $response['status'] = strtolower($order['Order_Status']);
    }
}

echo json_encode($response);
?>
