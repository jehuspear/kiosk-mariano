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
require_once 'database_customer.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function
function logDebug($message) {
    $logFile = __DIR__ . '/cart_debug.log';
    error_log(date('Y-m-d H:i:s') . " - " . print_r($message, true) . "\n", 3, $logFile);
}

// Ensure proper content type for JSON responses
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logDebug("POST request received");
    
    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
        logDebug("Cart initialized in session");
    }

    // Get POST data
    $rawData = file_get_contents('php://input');
    logDebug("Raw POST data: " . $rawData);
    
    $data = json_decode($rawData, true);
    logDebug("Decoded data: " . print_r($data, true));

    if ($data && isset($data['id'], $data['name'], $data['price'], $data['size'], $data['orderType'], $data['quantity'], $data['image'])) {
        // Create cart item
        $cartItem = array(
            'id' => $data['id'],
            'name' => $data['name'],
            'price' => floatval($data['price']),
            'size' => $data['size'],
            'orderType' => $data['orderType'],
            'quantity' => intval($data['quantity']),
            'image' => $data['image']
        );
        logDebug("Cart item created: " . print_r($cartItem, true));

        // Add to cart session
        $_SESSION['cart'][] = $cartItem;
        logDebug("Item added to cart. Current cart: " . print_r($_SESSION['cart'], true));

        // Calculate total quantity across all items
        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }
        logDebug("Total quantity calculated: " . $totalQuantity);

        $response = [
            'success' => true,
            'message' => 'Item added to cart',
            'cartCount' => $totalQuantity
        ];
        logDebug("Success response: " . print_r($response, true));
        echo json_encode($response);
        exit;
    } else {
        logDebug("Invalid data received: Missing required fields");
        echo json_encode([
            'success' => false,
            'message' => 'Invalid data: Missing required fields',
            'received' => $data
        ]);
        exit;
    }
}

$response = [
    'success' => false,
    'message' => 'Invalid request method or no data received'
];
logDebug("Error response: " . print_r($response, true));
echo json_encode($response);
