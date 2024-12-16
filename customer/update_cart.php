<?php
session_start();
require_once 'database_customer.php';

$response = array('success' => false);

try {
    // Check if cart exists and index and change are provided
    if (isset($_SESSION['cart']) && isset($_POST['index']) && isset($_POST['change'])) {
        $index = intval($_POST['index']);
        $change = intval($_POST['change']);
        
        // Check if index exists in cart
        if (isset($_SESSION['cart'][$index])) {
            // Calculate new quantity
            $newQuantity = $_SESSION['cart'][$index]['quantity'] + $change;
            
            // Ensure quantity doesn't go below 1
            if ($newQuantity >= 1) {
                $_SESSION['cart'][$index]['quantity'] = $newQuantity;
                
                // Calculate new total
                $totalAmount = 0;
                $totalQuantity = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $totalAmount += $item['price'] * $item['quantity'];
                    $totalQuantity += $item['quantity'];
                }
                
                $response['success'] = true;
                $response['newQuantity'] = $newQuantity;
                $response['newTotal'] = $totalAmount;
                $response['totalQuantity'] = $totalQuantity;
            }
        }
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
