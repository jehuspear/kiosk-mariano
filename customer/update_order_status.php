<?php
session_start();
require_once 'database_customer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$ticketNumber = isset($_POST['ticket_number']) ? intval($_POST['ticket_number']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$ticketNumber || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Set timezone and get current datetime
date_default_timezone_set('Asia/Manila');
$currentDateTime = date('Y-m-d H:i:s');

// Update order status
$sql = "UPDATE `order` SET Order_Status = ? WHERE Order_TicketNumber = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $ticketNumber);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
