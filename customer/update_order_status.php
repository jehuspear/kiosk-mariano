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

// Update order status
$sql = "UPDATE `order` SET Order_Status = ?, Order_CompletedTime = NOW() WHERE Order_TicketNumber = ?";
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
