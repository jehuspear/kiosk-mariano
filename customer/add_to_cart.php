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

// Function to log debug information
function logDebug($message) {
    $logFile = 'cart_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

$response = array('success' => false);

try {
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
        // Check stock availability
        $sql = "SELECT MenuItemSize_Stock as stock, MenuItemSize_IsHot as temperature 
                FROM menuitem_sizes 
                WHERE MenuItem_ID = ? AND MenuItemSize_SizeName = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare stock check statement');
        }
        
        $stmt->bind_param("is", $data['id'], $data['size']);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute stock check');
        }
        
        $result = $stmt->get_result();
        $sizeInfo = $result->fetch_assoc();
        
        if (!$sizeInfo) {
            throw new Exception('Size not found for this item');
        }
        
        if ($sizeInfo['stock'] < $data['quantity']) {
            $response['success'] = false;
            $response['error'] = 'Not enough stock available';
            logDebug("Insufficient stock: requested {$data['quantity']}, available {$sizeInfo['stock']}");
            echo json_encode($response);
            exit;
        }

        // Create cart item
        $cartItem = array(
            'id' => intval($data['id']),
            'name' => $data['name'],
            'image' => $data['image'],
            'size' => $data['size'],
            'price' => floatval($data['price']),
            'quantity' => intval($data['quantity']),
            'orderType' => $data['orderType'],
            'temperature' => $sizeInfo['temperature']
        );
        
        // Add to cart
        $_SESSION['cart'][] = $cartItem;
        logDebug("Item added to cart: " . print_r($cartItem, true));
        
        
        // Calculate totals
        $totalAmount = 0;
        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }
        
        $response['success'] = true;
        $response['cartCount'] = $totalQuantity;
        $response['totalAmount'] = $totalAmount;
        $response['message'] = 'Item added to cart successfully';
        logDebug("New total quantity: $totalQuantity");
    } else {
        $response['error'] = 'Missing required data';
        logDebug("Missing required data in request");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    logDebug("Error: " . $e->getMessage());
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
logDebug("Response sent: " . json_encode($response));
?>
