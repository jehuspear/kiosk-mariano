<?php
require_once 'database_customer.php';

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = isset($_POST['itemId']) ? intval($_POST['itemId']) : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;

    if ($itemId && $size) {
        // Query to get price and stock for the specific size
        $sql = "SELECT MenuItemSize_Price as price, MenuItemSize_Stock as stock 
                FROM menuitem_sizes 
                WHERE MenuItem_ID = ? AND MenuItemSize_Size = ?";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to prepare statement'
            ]);
            exit;
        }
        
        $stmt->bind_param("is", $itemId, $size);
        
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to execute statement'
            ]);
            $stmt->close();
            exit;
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            echo json_encode([
                'success' => true,
                'price' => $row['price'],
                'stock' => $row['stock']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Size not found'
            ]);
        }
        
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Item ID and size are required'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
