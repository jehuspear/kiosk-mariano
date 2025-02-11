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

    if ($data && isset($data['id'], $data['name'], $data['price'], $data['size'], $data['sizeId'], $data['orderType'], $data['quantity'], $data['image'])) {
        // Check stock availability
        $sql = "SELECT MenuItemSize_Stock as stock, MenuItemSize_IsHot as temperature 
                FROM menuitem_sizes 
                WHERE MenuItemSize_ID = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare stock check statement');
        }
        
        $stmt->bind_param("i", $data['sizeId']);
        
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

        // Check if identical item exists in cart
        $existingItemIndex = -1;
        foreach ($_SESSION['cart'] as $index => $item) {
            if (isset($item['sizeId']) && 
                $item['sizeId'] === intval($data['sizeId']) && 
                $item['id'] === intval($data['id']) && 
                $item['size'] === $data['size'] && 
                $item['orderType'] === $data['orderType']) {
                // Get temperature for the current item
                $sql = "SELECT MenuItemSize_IsHot as temperature 
                        FROM menuitem_sizes 
                        WHERE MenuItemSize_ID = ?";
                $tempStmt = $conn->prepare($sql);
                $tempStmt->bind_param("i", $item['sizeId']);
                $tempStmt->execute();
                $tempResult = $tempStmt->get_result();
                $tempInfo = $tempResult->fetch_assoc();
                
                // Only combine if temperatures match
                if ($tempInfo && $tempInfo['temperature'] === $sizeInfo['temperature']) {
                    $existingItemIndex = $index;
                    break;
                }
            }
        }

        // Create cart item
        $cartItem = array(
            'id' => intval($data['id']),
            'name' => $data['name'],
            'image' => $data['image'],
            'size' => $data['size'],
            'sizeId' => intval($data['sizeId']),
            'price' => floatval($data['price']),
            'quantity' => intval($data['quantity']),
            'orderType' => $data['orderType'],
            'temperature' => $sizeInfo['temperature']
        );
        
        if ($existingItemIndex !== -1) {
            // Update quantity of existing item
            $newQuantity = $_SESSION['cart'][$existingItemIndex]['quantity'] + $cartItem['quantity'];
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $sizeInfo['stock']) {
                $response['success'] = false;
                $response['error'] = 'Not enough stock available';
                logDebug("Insufficient stock: requested {$newQuantity}, available {$sizeInfo['stock']}");
                echo json_encode($response);
                exit;
            }
            
            $_SESSION['cart'][$existingItemIndex]['quantity'] = $newQuantity;
            logDebug("Updated existing item quantity: " . print_r($_SESSION['cart'][$existingItemIndex], true));
        } else {
            // Add as new item
            $_SESSION['cart'][] = $cartItem;
            logDebug("Added new item to cart: " . print_r($cartItem, true));
        }
        
        
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
