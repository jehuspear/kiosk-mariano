<?php
require_once 'database_customer.php';
require_once 'get_best_sellers.php';

function getAllMenuItems() {
    global $conn;
    
    // First get top 3 best sellers
    $bestSellersQuery = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, 
                   m.MenuItem_Description, m.MenuItem_Category,
                   m.MenuItem_TotalStocks, m.MenuItem_TotalSold,
                   MIN(ms.MenuItemSize_Price) as base_price
            FROM menuitem m
            LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
            GROUP BY m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, 
                     m.MenuItem_Description, m.MenuItem_Category,
                     m.MenuItem_TotalStocks, m.MenuItem_TotalSold
            ORDER BY m.MenuItem_TotalSold DESC
            LIMIT 3";
    
    $bestSellersResult = mysqli_query($conn, $bestSellersQuery);
    if (!$bestSellersResult) {
        die("Best sellers query failed: " . mysqli_error($conn));
    }
    
    // Get IDs of best sellers to exclude them from the main query
    $bestSellerIds = [];
    $items = [];
    
    while ($row = mysqli_fetch_assoc($bestSellersResult)) {
        $bestSellerIds[] = $row['MenuItem_ID'];
        $imagePath = '../admin-staff/' . $row['MenuItem_Image'];
        
        $items[] = array(
            'id' => $row['MenuItem_ID'],
            'name' => $row['MenuItem_Name'],
            'image' => $imagePath,
            'description' => $row['MenuItem_Description'],
            'category' => $row['MenuItem_Category'],
            'price' => $row['base_price'],
            'stock' => $row['MenuItem_TotalStocks'],
            'total_sold' => $row['MenuItem_TotalSold']
        );
    }
    
    // Now get remaining items sorted by category in specific order and then by name
    $remainingQuery = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, 
                   m.MenuItem_Description, m.MenuItem_Category,
                   m.MenuItem_TotalStocks, m.MenuItem_TotalSold,
                   MIN(ms.MenuItemSize_Price) as base_price,
                   CASE m.MenuItem_Category
                       WHEN 'Traditional Coffee' THEN 1
                       WHEN 'Coffee' THEN 2
                       WHEN 'Non-Coffee' THEN 3
                       WHEN 'Mocktail' THEN 4
                       WHEN 'Pastries' THEN 5
                       WHEN 'Snacks' THEN 6
                       ELSE 7
                   END as category_order
            FROM menuitem m
            LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
            WHERE m.MenuItem_ID NOT IN (" . implode(',', array_map('intval', $bestSellerIds)) . ")
            GROUP BY m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, 
                     m.MenuItem_Description, m.MenuItem_Category,
                     m.MenuItem_TotalStocks, m.MenuItem_TotalSold
            ORDER BY category_order, m.MenuItem_Name";
            
    $remainingResult = mysqli_query($conn, $remainingQuery);
    if (!$remainingResult) {
        die("Remaining items query failed: " . mysqli_error($conn));
    }
    
    // Add remaining items to the array
    while ($row = mysqli_fetch_assoc($remainingResult)) {
        $imagePath = '../admin-staff/' . $row['MenuItem_Image'];
        
        $items[] = array(
            'id' => $row['MenuItem_ID'],
            'name' => $row['MenuItem_Name'],
            'image' => $imagePath,
            'description' => $row['MenuItem_Description'],
            'category' => $row['MenuItem_Category'],
            'price' => $row['base_price'],
            'stock' => $row['MenuItem_TotalStocks'],
            'total_sold' => $row['MenuItem_TotalSold']
        );
    }
    
    return $items;
}

function displayMenuItems() {
    $items = getAllMenuItems();
    
    foreach ($items as $item) {
        // Check if item is out of stock
        $outOfStock = $item['stock'] <= 0;
        
        // Check if item is a best seller and get its rank
        $bestSellerRank = getBestSellerRank($item['id']);
        $bestSellerBadge = '';
        
        if ($bestSellerRank) {
            $bestSellerBadge = sprintf(
                '<div class="best-seller-badge best-seller-rank-%d">
                    <i class="fas fa-star"></i>
                    Best Seller #%d
                </div>',
                $bestSellerRank,
                $bestSellerRank
            );
        }
        ?>
        <div class="menu-item <?php echo $outOfStock ? 'out-of-stock-item' : ''; ?>" 
                 <?php if (!$outOfStock): ?>
                 data-bs-toggle="modal" 
                 data-bs-target="#itemModal"
                 data-item-id="<?php echo $item['id']; ?>"
                 onclick="showDetails('<?php echo htmlspecialchars($item['name']); ?>', 
                                    <?php echo $item['price']; ?>, 
                                    '<?php echo str_replace(array("\r\n", "\n", "\r"), " ", addslashes(htmlspecialchars($item['description']))); ?>',
                                    '<?php echo htmlspecialchars($item['image']); ?>',
                                    <?php echo $outOfStock ? 'true' : 'false'; ?>,
                                    <?php echo $item['id']; ?>)"
                 <?php endif; ?>>
                <?php echo $bestSellerBadge; ?>
                <div class="menu-item-image">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php if ($outOfStock): ?>
                    <div class="out-of-stock">
                        <i class="fas fa-exclamation-circle"></i>
                        Out of Stock
                    </div>
                    <?php endif; ?>
                </div>
                <div class="item-details">
                    <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p class="item-category"><?php echo htmlspecialchars($item['category']); ?></p>
                    <p class="item-price">
                        <span class="currency">₱</span>
                        <span class="amount"><?php echo number_format($item['price'], 2); ?></span>
                    </p>
                </div>
        </div>
        <?php
    }
}
?>
