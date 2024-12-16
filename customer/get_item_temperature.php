<?php
require_once 'database_customer.php';

function getItemTemperatures($itemId) {
    global $conn;
    
    $sql = "SELECT DISTINCT 
                CASE 
                    WHEN MenuItemSize_Temperature = 1 THEN 'Hot'
                    WHEN MenuItemSize_Temperature = 0 THEN 'Cold'
                END as temperature
            FROM menuitem_sizes 
            WHERE MenuItem_ID = ?
            ORDER BY MenuItemSize_Temperature DESC";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $temperatures = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $temperatures[] = $row['temperature'];
    }
    
    mysqli_stmt_close($stmt);
    return $temperatures;
}
?>
