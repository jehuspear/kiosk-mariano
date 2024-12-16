<?php
require_once 'database_customer.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Log function
function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, "debug.log");
}

if (isset($_GET['itemId'])) {
    $itemId = intval($_GET['itemId']);
    
    logError("Received request for itemId: " . $itemId);
    
    // Query to get sizes and their temperatures for this item
    $sql = "SELECT 
                MenuItemSize_Size as size, 
                MenuItemSize_IsHot as isHot,
                MenuItemSize_Stock as stock,
                MenuItemSize_Price as price
            FROM menuitem_sizes 
            WHERE MenuItem_ID = ?
            ORDER BY CASE MenuItemSize_Size
                WHEN 'Uno' THEN 1
                WHEN 'Dos' THEN 2
                WHEN 'Tres' THEN 3
                WHEN 'Quatro' THEN 4
                WHEN 'Sinco' THEN 5
            END";
    
    logError("SQL Query: " . $sql);
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        logError("Prepare failed: " . $conn->error);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to prepare statement: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param("i", $itemId);
    
    if (!$stmt->execute()) {
        logError("Execute failed: " . $stmt->error);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to execute statement: ' . $stmt->error
        ]);
        $stmt->close();
        exit;
    }
    
    $result = $stmt->get_result();
    $sizes = [];
    
    while ($row = $result->fetch_assoc()) {
        $sizes[] = [
            'size' => $row['size'],
            'temperature' => $row['isHot'] ? 'HOT' : 'COLD',
            'stock' => $row['stock'],
            'price' => $row['price']
        ];
    }
    
    $stmt->close();
    
    if (empty($sizes)) {
        logError("No sizes found for itemId: " . $itemId);
        echo json_encode([
            'success' => false, 
            'message' => 'No available sizes found for item ID: ' . $itemId
        ]);
        exit;
    }
    
    logError("Successfully found " . count($sizes) . " sizes for itemId: " . $itemId);
    echo json_encode([
        'success' => true,
        'sizes' => $sizes
    ]);
} else {
    logError("No itemId provided in request");
    echo json_encode([
        'success' => false, 
        'message' => 'Item ID is required'
    ]);
}
?>
