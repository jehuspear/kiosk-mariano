<?php
require_once 'database_customer.php';

function getTemperatureOptions($itemId) {
    global $conn;
    
    $sql = "SELECT DISTINCT 
                CASE 
                    WHEN MenuItemSize_Temperature = 1 THEN 'Hot'
                    WHEN MenuItemSize_Temperature = 0 THEN 'Cold'
                END as temperature,
                MenuItemSize_Size as size,
                MenuItemSize_Price as price,
                MenuItemSize_Stock as stock
            FROM menuitem_sizes 
            WHERE MenuItem_ID = ?
            ORDER BY MenuItemSize_Temperature DESC, MenuItemSize_Size";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $options = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $key = $row['temperature'];
        if (!isset($options[$key])) {
            $options[$key] = array(
                'temperature' => $row['temperature'],
                'sizes' => array()
            );
        }
        $options[$key]['sizes'][] = array(
            'size' => $row['size'],
            'price' => $row['price'],
            'stock' => $row['stock']
        );
    }
    
    mysqli_stmt_close($stmt);
    return array_values($options);
}

// If this file is called directly via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['itemId'])) {
    header('Content-Type: application/json');
    $itemId = intval($_GET['itemId']);
    $options = getTemperatureOptions($itemId);
    echo json_encode($options);
}
?>
