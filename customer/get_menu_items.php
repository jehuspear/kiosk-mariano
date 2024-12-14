<?php
require_once 'database_customer.php';
require_once 'get_best_sellers.php';

function getAllMenuItems() {
    global $conn;
    
    // Query to get all menu items with their base prices (Uno size)
    $sql = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, 
                   m.MenuItem_Description, m.MenuItem_Category,
                   m.MenuItem_TotalStocks, m.MenuItem_TotalSold,
                   ms.MenuItemSize_Price as base_price
            FROM menuitem m
            LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
            WHERE ms.MenuItemSize_Size = 'Uno'
            ORDER BY m.MenuItem_Category, m.MenuItem_Name";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $items = array();
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Prepend the correct path to admin-staff directory
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
        <div class="col">
            <div class="menu-item <?php echo $outOfStock ? 'out-of-stock-item' : ''; ?>" 
                 <?php if (!$outOfStock): ?>
                 data-bs-toggle="modal" 
                 data-bs-target="#itemModal" 
                 onclick="showDetails('<?php echo htmlspecialchars($item['name']); ?>', 
                                    <?php echo $item['price']; ?>, 
                                    '<?php echo htmlspecialchars($item['description']); ?>',
                                    '<?php echo htmlspecialchars($item['image']); ?>',
                                    <?php echo $outOfStock ? 'true' : 'false'; ?>,
                                    <?php echo $item['id']; ?>)"
                 <?php endif; ?>>
                <?php if ($outOfStock): ?>
                <div class="out-of-stock">
                    <i class="fas fa-exclamation-circle"></i>
                    Out of Stock
                </div>
                <?php endif; ?>
                <?php echo $bestSellerBadge; ?>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="item-details">
                    <p class="item-name"><?php echo htmlspecialchars($item['name']); ?></p>
                    <p class="item-category"><?php echo htmlspecialchars($item['category']); ?></p>
                    <p class="item-price">₱<?php echo number_format($item['price'], 2); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
