<?php
session_start();
require_once 'database_customer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['subscription']) || !isset($data['ticketNumber'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
        exit;
    }

    // Store subscription in session instead of database
    $_SESSION['push_subscription'] = [
        'ticket_number' => $data['ticketNumber'],
        'subscription' => $data['subscription']
    ];
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
