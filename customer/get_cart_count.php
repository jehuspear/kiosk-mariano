<?php
session_start();

// Initialize response array
$response = array(
    'success' => true,
    'count' => 0
);

// Get total quantity from cart session
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $totalQuantity = 0;
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['quantity'])) {
            $totalQuantity += (int)$item['quantity'];
        }
    }
    $response['count'] = $totalQuantity;
}

// Send JSON response
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
echo json_encode($response);
