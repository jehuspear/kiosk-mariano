<?php
session_start();
require_once 'database_customer.php';

$response = array('success' => false);

try {
    // Check if cart exists in session
    if (isset($_SESSION['cart'])) {
        // Clear the cart
        unset($_SESSION['cart']);
        
        // Clear ticket number if exists
        if (isset($_SESSION['ticket_number'])) {
            unset($_SESSION['ticket_number']);
        }
        
        $response['success'] = true;
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
