<?php
session_start();
require_once 'database_admin.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to prevent any unwanted output
ob_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to view receipts'
    ]);
    exit;
}

// Validate order ID
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Valid order ID is required'
    ]);
    exit;
}

$orderId = intval($_GET['order_id']);

// Log the request for debugging
error_log("Fetching receipt for order ID: " . $orderId);

try {
    // Get order details with error handling
    $sql = "SELECT o.*, p.*, s.Staff_FirstName, p.Payment_ReferenceNumber 
            FROM `order` o 
            JOIN payment p ON o.Payment_ID = p.Payment_ID 
            JOIN staff s ON o.Staff_ID = s.Staff_ID 
            WHERE o.Order_ID = ?";

    error_log("SQL Query: " . $sql);
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare order query: " . $conn->error);
    }

    $stmt->bind_param("i", $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute order query: " . $stmt->error);
    }

    $orderResult = $stmt->get_result()->fetch_assoc();
    if (!$orderResult) {
        throw new Exception("Order #$orderId not found");
    }

    error_log("Found order: " . json_encode($orderResult));

    // Get order items with error handling, including temperature details
    $sql = "SELECT oi.*, m.MenuItem_Name, ms.MenuItemSize_IsHot 
            FROM orderitem oi 
            JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID 
            LEFT JOIN menuitem_sizes ms ON oi.MenuItemSize_ID = ms.MenuItemSize_ID
            WHERE oi.Order_ID = ?";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare items query: " . $conn->error);
    }

    $stmt->bind_param("i", $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute items query: " . $stmt->error);
    }

    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (empty($items)) {
        throw new Exception("No items found for order #$orderId");
    }

    error_log("Found items: " . json_encode($items));

    // Prepare response data
    $response = [
        'success' => true,
        'receipt' => [
            'orderId' => $orderId,
            'ticketNumber' => str_pad($orderResult['Order_TicketNumber'], 3, '0', STR_PAD_LEFT),
            'dateTime' => $orderResult['Order_DateTime'],
            'staffName' => $orderResult['Staff_FirstName'],
            'eatingOption' => $orderResult['Order_EatingOption'],
            'paymentMethod' => $orderResult['Payment_Method'],
            'items' => array_map(function($item) {
                return [
                    'name' => $item['MenuItem_Name'],
                    'size' => $item['OrderItem_CupSize'],
                    'temperature' => $item['MenuItemSize_IsHot'] ?? 'Normal',
                    'quantity' => $item['OrderItem_Quantity'],
                    'price' => $item['OrderItem_Price']
                ];
            }, $items),
            'subtotal' => floatval($orderResult['Order_TotalAmount']),
            'discountType' => $orderResult['Payment_DiscountType'],
            'discountAmount' => floatval($orderResult['Payment_DiscountAmount']),
            'total' => floatval($orderResult['Payment_TotalAmount']),
            'cashAmount' => $orderResult['Payment_Method'] === 'Cash' ? floatval($orderResult['Payment_CashPaid']) : null,
            'change' => $orderResult['Payment_Method'] === 'Cash' ? floatval($orderResult['Payment_Change']) : null,
            'referenceNumber' => $orderResult['Payment_ReferenceNumber']
        ]
    ];

    error_log("Sending response: " . json_encode($response));

    // Clear any buffered output and send response
    ob_end_clean();
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Receipt error: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'details' => 'Please contact support if this error persists'
    ]);
}
?>
