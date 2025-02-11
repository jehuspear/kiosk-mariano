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
            // Get current cart item
            $cartItem = $_SESSION['cart'][$index];
            
            // Calculate new quantity
            $newQuantity = $cartItem['quantity'] + $change;
            
            // If increasing quantity, check stock availability
            if ($change > 0) {
                // Check stock availability and temperature
                $sql = "SELECT MenuItemSize_Stock as stock, MenuItemSize_IsHot as temperature 
                        FROM menuitem_sizes 
                        WHERE MenuItemSize_ID = ?";
                
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Failed to prepare stock check statement');
                }
                
                $stmt->bind_param("i", $cartItem['sizeId']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to execute stock check');
                }
                
                $result = $stmt->get_result();
                $sizeInfo = $result->fetch_assoc();
                
                if (!$sizeInfo) {
                    throw new Exception('Size not found for this item');
                }
                
                // Verify temperature matches
                if ($sizeInfo['temperature'] !== $cartItem['temperature']) {
                    $response['success'] = false;
                    $response['error'] = 'Item temperature mismatch';
                    echo json_encode($response);
                    exit;
                }

                if ($sizeInfo['stock'] < $newQuantity) {
                    $response['success'] = false;
                    $response['error'] = 'Not enough stock available';
                    echo json_encode($response);
                    exit;
                }
            }
            
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
