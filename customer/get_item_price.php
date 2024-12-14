<?php
require_once 'database_customer.php';

if (isset($_POST['itemId']) && isset($_POST['size'])) {
    $itemId = mysqli_real_escape_string($conn, $_POST['itemId']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    
    // Query to get price for specific item and size
    $sql = "SELECT MenuItemSize_Price, MenuItemSize_Stock 
            FROM menuitem_sizes 
            WHERE MenuItem_ID = '$itemId' 
            AND MenuItemSize_Size = '$size'";
            
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            'success' => true,
            'price' => $row['MenuItemSize_Price'],
            'stock' => $row['MenuItemSize_Stock']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Price not found for selected size'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters'
    ]);
}
?>
