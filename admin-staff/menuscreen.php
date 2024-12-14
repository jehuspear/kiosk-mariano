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
        m.MenuItem_Category, m.MenuItem_TotalStocks, m.MenuItem_TotalSold,
        ms.MenuItemSize_ID, ms.MenuItemSize_Size, ms.MenuItemSize_Price, 
        ms.MenuItemSize_IsHot, ms.MenuItemSize_Stock
        FROM menuitem m
        LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
        ORDER BY m.MenuItem_ID, 
        CASE ms.MenuItemSize_Size 
            WHEN 'Uno' THEN 1 
            WHEN 'Dos' THEN 2 
            WHEN 'Tres' THEN 3 
            WHEN 'Quatro' THEN 4 
            WHEN 'Sinco' THEN 5 
        END";
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
            'sizes' => array()
        );
    }
    if ($row['MenuItemSize_ID']) {
        $menuItems[$itemId]['sizes'][] = array(
            'id' => $row['MenuItemSize_ID'],
            'size' => $row['MenuItemSize_Size'],
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
  <link rel="stylesheet" href="Css-admin/menuscreen.css">
  <link rel="stylesheet" href="Css-admin/modal.css">
  <link rel="stylesheet" href="Css-admin/delete-modal.css">
  <link rel="stylesheet" href="Css-admin/menu-sizes.css">
  <link rel="stylesheet" href="Css-admin/menu-items.css">
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <style>
    /* Custom Styles */
    .wrapper {
      display: flex;
      min-height: 100vh;
      
    }

    /* Sidebar Styles */
    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      padding: 20px;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
    }

    .sidebar .logo {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar .nav {
      list-style: none;
      padding: 0;
    }

    .sidebar .nav li {
      margin-bottom: 10px;
    }

    .sidebar .nav a {
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .sidebar .nav a:hover,
    .sidebar .nav li.active a {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav i {
      margin-right: 10px;
      width: 20px;
    }

    /* Main Content Layout */
    .main-content {
      flex: 1;
      margin-left: 250px;
      padding: 20px;
    }

    /* Header Menu Styles */
    .header-menu {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 20px;
      background-color: #343a40;
      padding: 15px;
      border-radius: 10px;
      z-index: 1000;
    }

    .menu-item {
      flex: 1;
      min-width: 150px;
      text-align: center;
    }

    .menu-item a {
      text-decoration: none;
      color: #343a40;
      display: block;
      padding: 10px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .menu-item.active a,
    .menu-item a:hover {
      background-color: #131213;
      color: white;
    }

    .menu-item i {
      font-size: 24px;
      margin-bottom: 5px;
    }

    .menu-item p {
      margin: 0;
    }

    .buttons {
      margin-top: 20px;
      margin-bottom: 20px;
    }

    .menu-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      padding: 10px;
    }

    .card {
      height: 100%;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-img-top {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .card-body {
      padding: 15px;
    }

    .card-title {
      font-size: 1.2rem;
      margin-bottom: 10px;
    }

    .status {
      width: 100%;
      margin: 10px 0;
    }

    .actions {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }

    .action-btn {
      flex: 1;
    }

    .add-menu-card {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 200px;
      border: 2px dashed #dee2e6;
      background-color: #f8f9fa;
    }

    .add-menu-container {
      text-align: center;
    }

    .add-menu-btn {
      font-size: 24px;
      margin-bottom: 10px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .sidebar {
        width: 60px;
        padding: 10px;
      }

      .sidebar .logo h2,
      .sidebar .nav span {
        display: none;
      }

      .main-content {
        margin-left: 60px;
      }

      .menu-item {
        min-width: 120px;
      }

      .card-title {
        font-size: 1rem;
      }
    }

    @media (max-width: 576px) {
      .menu-cards {
        grid-template-columns: 1fr;
      }

      .header-menu {
        flex-direction: column;
        gap: 5px;
      }

      .menu-item {
        width: 100%;
      }

      .buttons {
        display: flex;
        gap: 10px;
      }

      .buttons button {
        flex: 1;
      }
    }
  </style>
</head>
<body>
  <!-- Modal Structure -->
  <div class="modal-overlay" id="modalOverlay"></div>
  <div class="modal-container" id="modalContainer">
    <button class="modal-close" onclick="closeModal()">&times;</button>
    <iframe id="modalIframe" class="modal-iframe" src=""></iframe>
  </div>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo">
        <h2>SINCO CAFE</h2>
      </div>
      <ul class="nav">
        <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i> <span>Logout</span></a></li>
        <li class="active"><a href="menuscreen.php"><i class="fa-solid fa-book"></i> <span>Menu</span></a></li>
        <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> <span>E-ticket</span></a></li>
        <li><a href="orderlist.php"><i class="fa-solid fa-sort"></i> <span>Order Lists</span></a></li>
        <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> <span>Orders</span></a></li>
        <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> <span>Completed</span></a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> <span>Reports</span></a></li>
        <li><a href="decodingscreen.php"><i class="fa-regular fa-comment"></i> <span>Feedback</span></a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> <span>History</span></a></li>
      </ul>
  </div>

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
    <!-- Previous sidebar content remains unchanged -->
    
    <!-- Main Content -->
    <div class="main-content">
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

      <!-- View & Edit Buttons -->
      <div class="buttons">
        <button class="btn btn-primary view-btn">
          <i class="fa-solid fa-eye"></i> View
        </button>
        <button class="btn btn-secondary edit-btn">
          <i class="fa-solid fa-pen"></i> Edit
        </button>
      </div>

      <!-- Menu Cards Section -->
      <div class="menu-cards">
        <?php
        foreach ($menuItems as $item) {
            if ($item['MenuItem_Category'] == $selectedCategory) {
                $availabilityClass = $item['MenuItem_TotalStocks'] > 0 ? 'btn-success' : 'btn-danger';
                $availabilityText = $item['MenuItem_TotalStocks'] > 0 ? 'AVAILABLE' : 'UNAVAILABLE';
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($item['sizes'] as $size): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($size['size']); ?></td>
                                        <td>
                                            <?php if ($size['is_hot']): ?>
                                                <span class="hot-label">HOT</span>
                                            <?php else: ?>
                                                <span class="cold-label">ICED</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>₱<?php echo number_format($size['price'], 2); ?></td>
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
                        <button class="btn <?php echo $availabilityClass; ?> status">
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
        <div class="card add-menu-card ">
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


  <!-- MODAL -->
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Menu Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="menuName" class="form-label">Name</label>
              <input type="text" class="form-control" id="menuName" value="Salted Caramel">
            </div>
            <div class="mb-3">
              <label for="menuPrice" class="form-label">Price</label>
              <input type="number" class="form-control" id="menuPrice" value="120">
            </div>
            <div class="mb-3">
              <label for="menuDescription" class="form-label">Description</label>
              <textarea class="form-control" id="menuDescription" rows="3">A delicious coffee drink with caramel flavor.</textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success">Save Changes</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap JS -->
  <script src="Css-admin/bootstrap.bundle.min.js"></script>
  
  <!-- Previous JavaScript code remains unchanged -->
  <script>
  let deleteItemId = null;

  function openModal(url) {
      document.getElementById('modalOverlay').classList.add('show');
      document.getElementById('modalContainer').classList.add('show');
      document.getElementById('modalIframe').src = url;
  }

  function closeModal() {
      document.getElementById('modalOverlay').classList.remove('show');
      document.getElementById('modalContainer').classList.remove('show');
      document.getElementById('modalIframe').src = '';
      // Reload the parent page to refresh the menu items
      window.location.reload();
  }

  function editMenuItem(menuItemId) {
      openModal('edit_menu_item.php?id=' + menuItemId);
  }

  function deleteMenuItem(menuItemId) {
      deleteItemId = menuItemId;
      document.getElementById('modalOverlay').classList.add('show');
      document.getElementById('deleteModal').classList.add('show');
  }

  function cancelDelete() {
      deleteItemId = null;
      document.getElementById('modalOverlay').classList.remove('show');
      document.getElementById('deleteModal').classList.remove('show');
  }

  function confirmDelete() {
      if (deleteItemId) {
          window.location.href = 'delete_menu_item.php?id=' + deleteItemId;
      }
  }

  function addMenuItem() {
      openModal('add_menu_item.php');
  }

  // Close modal when clicking outside
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
          if (document.getElementById('deleteModal').classList.contains('show')) {
              cancelDelete();
          } else {
              closeModal();
          }
      }
  });

  // Close modal with escape key
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          if (document.getElementById('deleteModal').classList.contains('show')) {
              cancelDelete();
          } else {
              closeModal();
          }
      }
  });
  </script>
</body>
</html>


