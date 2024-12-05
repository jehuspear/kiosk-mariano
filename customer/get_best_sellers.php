<?php
require_once 'database_customer.php';

function getBestSellingItems($limit = 3) {
    global $conn;
    
    // Query to get items with highest total sold count
    $sql = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_TotalSold 
            FROM menuitem m 
            WHERE m.MenuItem_TotalSold > 0 
            ORDER BY m.MenuItem_TotalSold DESC 
            LIMIT ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $bestSellers = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bestSellers[$row['MenuItem_ID']] = array(
                'name' => $row['MenuItem_Name'],
                'total_sold' => $row['MenuItem_TotalSold']
            );
        }
    }
    
    mysqli_stmt_close($stmt);
    return $bestSellers;
}

function isBestSeller($itemId) {
    static $bestSellers = null;
    
    // Cache best sellers to avoid multiple database queries
    if ($bestSellers === null) {
        $bestSellers = getBestSellingItems();
    }
    
    return isset($bestSellers[$itemId]);
}

function getBestSellerRank($itemId) {
    static $bestSellers = null;
    
    // Cache best sellers to avoid multiple database queries
    if ($bestSellers === null) {
        $bestSellers = getBestSellingItems();
    }
    
    if (isset($bestSellers[$itemId])) {
        return array_search($itemId, array_keys($bestSellers)) + 1;
    }
    
    return false;
}
?>
