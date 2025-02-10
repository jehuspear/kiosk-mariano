<?php
session_start();
header('Content-Type: application/json');

// Get today's date range in Manila time
date_default_timezone_set('Asia/Manila');
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

$hasValidTicket = false;

if (isset($_SESSION['ticket_number'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kiosk_ordering_system_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if (!$conn->connect_error) {
        // Check if the ticket exists, is completed, and hasn't already received feedback
        $stmt = $conn->prepare("SELECT o.Order_ID, o.Order_Status,
                              (SELECT COUNT(*) FROM feedback f WHERE f.Order_ID = o.Order_ID) as feedback_count
                              FROM `order` o
                              WHERE o.Order_TicketNumber = ? 
                              AND o.Order_DateTime BETWEEN ? AND ?
                              LIMIT 1");
        
        $stmt->bind_param("iss", $_SESSION['ticket_number'], $today_start, $today_end);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Check both completion status and feedback existence
            $hasValidTicket = $row['Order_Status'] === 'Completed' && $row['feedback_count'] == 0;
            if ($row['Order_Status'] !== 'Completed') {
                $message = 'Feedback can only be submitted for completed orders';
            } else if ($row['feedback_count'] > 0) {
                $message = 'Feedback has already been submitted for this order';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

echo json_encode([
    'hasTicket' => $hasValidTicket,
    'ticketNumber' => $_SESSION['ticket_number'] ?? null,
    'message' => $hasValidTicket ? null : ($message ?? 'Invalid ticket number')
]);
