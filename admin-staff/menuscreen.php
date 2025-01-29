<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include 'database_admin.php';

// Fetch menu items and their sizes from database
$sql = "SELECT m.MenuItem_ID, m.MenuItem_Name, m.MenuItem_Image, m.MenuItem_Description, 
        m.MenuItem_Category, m.MenuItem_TotalStocks, m.MenuItem_TotalSold, m.MenuItem_Availability,
        ms.MenuItemSize_ID, ms.MenuItemSize_SizeName, ms.MenuItemSize_Price, 
        ms.MenuItemSize_IsHot, ms.MenuItemSize_Stock
        FROM menuitem m
        LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
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
            'MenuItem_Category' => $row['MenuItem_Category'],
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

// Get the currently selected category (default to 'Traditional Coffee')
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'Traditional Coffee';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management Screen</title>
    
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
                    <div class="menu-item <?php echo $selectedCategory == 'Traditional Coffee' ? 'active' : ''; ?>">
                        <a href="?category=Traditional Coffee">
                            <i class="fa-solid fa-coffee"></i>
                            <p>Traditional Coffee</p>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $selectedCategory == 'Coffee' ? 'active' : ''; ?>">
                        <a href="?category=Coffee">
                            <i class="fa-solid fa-mug-hot"></i>
                            <p>Coffee</p>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $selectedCategory == 'Non-Coffee' ? 'active' : ''; ?>">
                        <a href="?category=Non-Coffee">
                            <i class="fa-solid fa-wine-glass"></i>
                            <p>Non-Coffee</p>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $selectedCategory == 'Mocktail' ? 'active' : ''; ?>">
                        <a href="?category=Mocktail">
                            <i class="fa-solid fa-cocktail"></i>
                            <p>Mocktail</p>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $selectedCategory == 'Pastries' ? 'active' : ''; ?>">
                        <a href="?category=Pastries">
                            <i class="fa-solid fa-cookie"></i>
                            <p>Pastries</p>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $selectedCategory == 'Snacks' ? 'active' : ''; ?>">
                        <a href="?category=Snacks">
                            <i class="fa-solid fa-pizza-slice"></i>
                            <p>Snacks</p>
                        </a>
                    </div>
                </div>

                <!-- Menu Cards Section -->
                <div class="menu-cards">
                    <?php
                    foreach ($menuItems as $item) {
                        if ($item['MenuItem_Category'] == $selectedCategory) {
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
