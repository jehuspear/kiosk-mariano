<?php
// Set session cookie parameters before starting session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

$response = array(
    'success' => true,
    'count' => 0,
    'totalAmount' => 0
);

// Calculate total quantity and amount if cart exists
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $totalQuantity = 0;
    $totalAmount = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $totalQuantity += $item['quantity'];
        $totalAmount += $item['price'] * $item['quantity'];
    }
    
    $response['count'] = $totalQuantity;
    $response['totalAmount'] = $totalAmount;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
