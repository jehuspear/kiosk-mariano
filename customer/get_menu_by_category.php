<?php
require_once 'database_customer.php';
require_once 'get_best_sellers.php';

if (isset($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    
    // Query to get menu items by category
    $sql = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, 
                   m.MenuItem_Description, m.MenuItem_Category,
                   m.MenuItem_TotalStocks, m.MenuItem_TotalSold,
                   ms.MenuItemSize_Price as base_price
            FROM menuitem m
            LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
            WHERE ms.MenuItemSize_Size = 'Uno'";
    
    // Add category filter if not "all"
    if ($category !== 'all') {
        $sql .= " AND m.MenuItem_Category = '$category'";
    }
    
    $sql .= " ORDER BY m.MenuItem_Category, m.MenuItem_Name";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo json_encode(['error' => mysqli_error($conn)]);
        exit;
    }
    
    $items = array();
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Prepend the correct path to admin-staff directory
            $imagePath = '../admin-staff/' . $row['MenuItem_Image'];
            
            // Check if item is a best seller
            $bestSellerRank = getBestSellerRank($row['MenuItem_ID']);
            
            $items[] = array(
                'id' => $row['MenuItem_ID'],
                'name' => $row['MenuItem_Name'],
                'image' => $imagePath,
                'description' => $row['MenuItem_Description'],
                'category' => $row['MenuItem_Category'],
                'price' => $row['base_price'],
                'stock' => $row['MenuItem_TotalStocks'],
                'total_sold' => $row['MenuItem_TotalSold'],
                'best_seller_rank' => $bestSellerRank
            );
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($items);
}
?>
