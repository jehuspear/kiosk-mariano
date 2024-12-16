<?php
require_once 'database_customer.php';

if (isset($_GET['itemId'])) {
    $itemId = intval($_GET['itemId']);
    
    // Query to get item sizes with their temperatures
    $sql = "SELECT 
                MenuItemSize_Size as size,
                MenuItemSize_IsHot as isHot,
                MenuItemSize_Price as price,
                MenuItemSize_Stock as stock
            FROM menuitem_sizes 
            WHERE MenuItem_ID = ?
            ORDER BY 
                CASE MenuItemSize_Size
                    WHEN 'Uno' THEN 1
                    WHEN 'Dos' THEN 2
                    WHEN 'Tres' THEN 3
                    WHEN 'Quatro' THEN 4
                    WHEN 'Sinco' THEN 5
                END";
            
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Failed to prepare statement: ' . mysqli_error($conn)
        ));
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    
    if (!mysqli_stmt_execute($stmt)) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Failed to execute statement: ' . mysqli_stmt_error($stmt)
        ));
        mysqli_stmt_close($stmt);
        exit;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Failed to get result: ' . mysqli_error($conn)
        ));
        mysqli_stmt_close($stmt);
        exit;
    }
    
    $sizes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $sizes[] = array(
            'size' => $row['size'],
            'temperature' => $row['isHot'] ? 'HOT' : 'ICED',
            'price' => floatval($row['price']),
            'stock' => intval($row['stock'])
        );
    }
    
    mysqli_stmt_close($stmt);
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => true,
        'sizes' => $sizes
    ));
} else {
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'Item ID is required'
    ));
}
?>
