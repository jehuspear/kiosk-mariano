<?php
session_start();
require_once 'database_admin.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = isset($_POST['action']) ? $_POST['action'] : 'fetch';
        
        // Always get the current ticket numbers first
        $select_sql = "SELECT Order_TicketNumber FROM `order` WHERE Order_Status = 'ReadyToClaim' ORDER BY Order_TicketNumber";
        $result = mysqli_query($conn, $select_sql);
        
        $ticket_numbers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ticket_numbers[] = str_pad($row['Order_TicketNumber'], 3, '0', STR_PAD_LEFT);
        }
        
        if ($action === 'update' && count($ticket_numbers) > 0) {
            // Update all orders with status 'ReadyToClaim' to 'Completed'
            $update_sql = "UPDATE `order` SET Order_Status = 'Completed' WHERE Order_Status = 'ReadyToClaim'";
            
            if (mysqli_query($conn, $update_sql)) {
                echo json_encode([
                    'success' => true,
                    'message' => count($ticket_numbers) . ' orders have been marked as completed',
                    'affected_rows' => count($ticket_numbers),
                    'ticket_numbers' => $ticket_numbers
                ]);
            } else {
                throw new Exception(mysqli_error($conn));
            }
        } else if ($action === 'fetch') {
            // Just return the ticket numbers for display
            echo json_encode([
                'success' => true,
                'message' => count($ticket_numbers) . ' orders ready to complete',
                'ticket_numbers' => $ticket_numbers
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No ready orders found to complete',
                'ticket_numbers' => []
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
