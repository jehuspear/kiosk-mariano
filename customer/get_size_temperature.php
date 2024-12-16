<?php
require_once 'database_customer.php';

if (isset($_GET['itemId'])) {
    $itemId = intval($_GET['itemId']);
    
    // Query to get sizes and their temperatures for this item
    $sql = "SELECT 
                MenuItemSize_Size as size, 
                MenuItemSize_IsHot as isHot,
                MenuItemSize_Stock as stock,
                MenuItemSize_Price as price
            FROM menuitem_sizes 
            WHERE MenuItem_ID = ? AND MenuItemSize_Stock > 0
            ORDER BY CASE MenuItemSize_Size
                WHEN 'Uno' THEN 1
                WHEN 'Dos' THEN 2
                WHEN 'Tres' THEN 3
                WHEN 'Quatro' THEN 4
                WHEN 'Sinco' THEN 5
            END";
            
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => false, 'message' => 'Failed to execute statement']);
        mysqli_stmt_close($stmt);
        exit;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $sizes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $sizes[] = [
            'size' => $row['size'],
            'temperature' => $row['isHot'] ? 'HOT' : 'COLD',
            'stock' => $row['stock'],
            'price' => $row['price']
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    if (empty($sizes)) {
        echo json_encode(['success' => false, 'message' => 'No available sizes found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'sizes' => $sizes
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
}
?>
