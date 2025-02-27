<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include access control
require_once 'check_admin_access.php';
include 'database_admin.php';

// Fetch categories from database
$categorySql = "SELECT Category_ID, Category_Name, Category_Description FROM category ORDER BY Category_ID ASC";
$categoryResult = mysqli_query($conn, $categorySql);

// Store categories in an array
$categories = array();
while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
    $categories[] = $categoryRow;
}

// Get the currently selected category (default to first category if available)
$defaultCategoryId = !empty($categories) ? $categories[0]['Category_ID'] : 1;
$selectedCategoryId = isset($_GET['category_id']) ? $_GET['category_id'] : $defaultCategoryId;

// Find the selected category name for display purposes
$selectedCategoryName = '';
foreach ($categories as $category) {
    if ($category['Category_ID'] == $selectedCategoryId) {
        $selectedCategoryName = $category['Category_Name'];
        break;
    }
}

// Fetch menu items and their sizes from database with category information
$sql = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, m.MenuItem_Description, 
        m.Category_ID, c.Category_Name, m.MenuItem_TotalStocks, m.MenuItem_TotalSold, m.MenuItem_Availability,
        ms.MenuItemSize_ID, ms.MenuItemSize_SizeName, ms.MenuItemSize_Price, 
        ms.MenuItemSize_IsHot, ms.MenuItemSize_Stock
        FROM menuitem m
        LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
        LEFT JOIN category c ON m.Category_ID = c.Category_ID
        ORDER BY m.MenuItem_ID ASC";
$result = mysqli_query($conn, $sql);

// Group menu items with their sizes
$menuItems = array();
while ($row = mysqli_fetch_assoc($result)) {
    $itemId = $row['MenuItem_ID'];
    if (!isset($menuItems[$itemId])) {
        $menuItems[$itemId] = array(
            'MenuItem_ID' => $row['MenuItem_ID'],
            'MenuItem_Name' => $row['MenuItem_Name'],
            'MenuItem_Image' => $row['MenuItem_Image'],
            'MenuItem_Description' => $row['MenuItem_Description'],
            'Category_ID' => $row['Category_ID'],
            'Category_Name' => $row['Category_Name'],
            'MenuItem_TotalStocks' => $row['MenuItem_TotalStocks'],
            'MenuItem_TotalSold' => $row['MenuItem_TotalSold'],
            'MenuItem_Availability' => $row['MenuItem_Availability'],
            'sizes' => array()
        );
    }
    if ($row['MenuItemSize_ID']) {
        $menuItems[$itemId]['sizes'][] = array(
            'id' => $row['MenuItemSize_ID'],
            'size' => $row['MenuItemSize_SizeName'],
            'price' => $row['MenuItemSize_Price'],
            'is_hot' => $row['MenuItemSize_IsHot'],
            'stock' => $row['MenuItemSize_Stock']
        );
    }
}

// Function to get appropriate icon for category
function getCategoryIcon($categoryName) {
    $icons = [
        'Coffee' => 'fa-mug-hot',
        'Specialty Drinks' => 'fa-glass-martini-alt',
        'Blended Beverages' => 'fa-blender',
        'Non-Coffee' => 'fa-wine-glass',
        'Add-Ons' => 'fa-plus-circle',
        'Sandwiches' => 'fa-bread-slice',
        'Pica-Pica' => 'fa-pizza-slice',
        'Rice Meals' => 'fa-utensils',
        'Extras' => 'fa-mortar-pestle',
        // Default icons for other categories
        'Traditional Coffee' => 'fa-coffee',
        'Mocktail' => 'fa-cocktail',
        'Pastries' => 'fa-cookie',
        'Snacks' => 'fa-hamburger'
    ];
    
    return isset($icons[$categoryName]) ? $icons[$categoryName] : 'fa-utensils';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management Screen</title>

    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="resources/favicon/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="resources/favicon/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="resources/favicon/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="resources/favicon/favicon_io/site.webmanifest"> 
    
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="Css-admin/menuscreen.css">
    <link rel="stylesheet" href="Css-admin/menuscreen-custom.css">
    <link rel="stylesheet" href="Css-admin/modal.css">
    <link rel="stylesheet" href="Css-admin/delete-modal.css">
    <link rel="stylesheet" href="Css-admin/menu-sizes.css">
    <link rel="stylesheet" href="Css-admin/menu-items.css">
    <link rel="stylesheet" href="Css-admin/availability-modal.css">
    <link rel="stylesheet" href="Css-admin/responsive-header-menu.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Modal Structure -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal-container" id="modalContainer">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <iframe id="modalIframe" class="modal-iframe" src=""></iframe>
    </div>

    <div class="wrapper">
        <?php 
        require_once 'includes/sidebar.php';
        renderSidebar('menu');
        ?>

        <!-- Delete Confirmation Modal -->
        <div class="delete-modal" id="deleteModal">
            <h3><i class="fas fa-exclamation-triangle"></i> Delete Menu Item</h3>
            <p>Are you sure you want to delete this menu item? This action cannot be undone.</p>
            <div class="btn-group">
                <button class="btn btn-secondary" onclick="cancelDelete()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>

        <div class="wrapper">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Mobile Menu Toggle -->
                <div class="mobile-menu-toggle d-lg-none">
                    <button class="btn btn-dark" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <!-- Header Menu -->
                <div class="header-menu">
                    <?php foreach ($categories as $category): ?>
                    <div class="menu-item <?php echo $selectedCategoryId == $category['Category_ID'] ? 'active' : ''; ?>">
                        <a href="?category_id=<?php echo $category['Category_ID']; ?>" title="<?php echo htmlspecialchars($category['Category_Description']); ?>">
                            <i class="fa-solid <?php echo getCategoryIcon($category['Category_Name']); ?>"></i>
                            <p><?php echo htmlspecialchars($category['Category_Name']); ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Menu Cards Section -->
                <div class="menu-cards">
                    <?php
                    $itemsFound = false;
                    foreach ($menuItems as $item) {
                        if ($item['Category_ID'] == $selectedCategoryId) {
                            $itemsFound = true;
                            // Check both stock and availability status
                            $isAvailable = $item['MenuItem_TotalStocks'] > 0 && $item['MenuItem_Availability'] === 'Available';
                            $availabilityClass = $isAvailable ? 'btn-success' : 'btn-danger';
                            $availabilityText = $isAvailable ? 'AVAILABLE' : 'UNAVAILABLE';
                            
                            // If stock is 0, automatically update availability in database
                            if ($item['MenuItem_TotalStocks'] <= 0) {
                                $updateSql = "UPDATE menuitem SET MenuItem_Availability = 'Unavailable' WHERE MenuItem_ID = ?";
                                $stmt = $conn->prepare($updateSql);
                                $stmt->bind_param("i", $item['MenuItem_ID']);
                                $stmt->execute();
                                $stmt->close();
                            }
                            ?>
                            <div class="card">
                                <img src="<?php echo htmlspecialchars($item['MenuItem_Image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['MenuItem_Name']); ?>" 
                                     class="card-img-top">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($item['MenuItem_Name']); ?></h3>
                                    <p class="card-text"><?php echo htmlspecialchars($item['MenuItem_Description']); ?></p>
                                    
                                    <!-- Sizes and Prices Table -->
                                    <div class="price-section">
                                        <table class="size-price-table">
                                            <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Type</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($item['sizes'] as $size): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($size['size']); ?></td>
                                                    <td>
                                                        <?php 
                                                        switch($size['is_hot']) {
                                                            case 'Hot':
                                                                echo '<span class="hot-label"><i class="fas fa-fire"></i> HOT</span>';
                                                                break;
                                                            case 'Iced':
                                                                echo '<span class="cold-label"><i class="fas fa-snowflake"></i> ICED</span>';
                                                                break;
                                                            default:
                                                                echo '<span class="normal-label"><i class="fas fa-thermometer-half"></i> NORMAL</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>₱<?php echo number_format($size['price'], 2); ?></td>
                                                    <td><?php echo $size['stock']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <p class="card-text">
                                        <small class="text-muted">
                                            Stock: <?php echo $item['MenuItem_TotalStocks']; ?> | 
                                            Sold: <?php echo $item['MenuItem_TotalSold']; ?>
                                        </small>
                                    </p>

                                    <!-- Button to View or Update the MenuItem_Availability of the Menu Item-->
                                    <button class="btn <?php echo $availabilityClass; ?> status" 
                                            data-menu-id="<?php echo $item['MenuItem_ID']; ?>"
                                            onclick="toggleAvailability(this, <?php echo $item['MenuItem_ID']; ?>)">
                                        <?php echo $availabilityText; ?>
                                    </button>
                                    <div class="actions">
                                        <button class="btn btn-warning action-btn" 
                                                onclick="editMenuItem(<?php echo $item['MenuItem_ID']; ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-danger action-btn" 
                                                onclick="deleteMenuItem(<?php echo $item['MenuItem_ID']; ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php if (!$itemsFound): ?>
                    <div class="no-items-message">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> No menu items found for the category "<?php echo htmlspecialchars($selectedCategoryName); ?>". 
                            <br>You can add a new menu item by clicking the "Add Menu" button below.
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Add Menu Card -->
                    <div class="card add-menu-card">
                        <div class="add-menu-container">
                            <button class="btn btn-outline-primary add-menu-btn" onclick="addMenuItem()">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <span class="add-menu-text">Add Menu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="Css-admin/bootstrap.bundle.min.js"></script>

    <!-- Javascript for Menuscreen -->
    <script src="Javascript-admin/menu-screen.js"></script>
    <script src="Javascript-admin/mobile-menu.js"></script>
    <script src="Javascript-admin/menu-availability.js"></script>
</body>
</html>
