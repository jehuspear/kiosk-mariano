<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include 'database_admin.php';

// Fetch menu items from database
$sql = "SELECT MenuItem_ID, MenuItem_Name, MenuItem_Image, MenuItem_Description, MenuItem_Category, MenuItem_TotalStocks, MenuItem_TotalSold FROM menuitem";
$result = mysqli_query($conn, $sql);

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
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <style>
    /* Custom Styles */
    .wrapper {
      display: flex;
      min-height: 100vh;
      
    }

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

    .main-content {
      flex: 1;
      margin-left: 250px;
      padding: 20px;
    }

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
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                // Only show items from selected category
                if ($row['MenuItem_Category'] == $selectedCategory) {
                    $availabilityClass = $row['MenuItem_TotalStocks'] > 0 ? 'btn-success' : 'btn-danger';
                    $availabilityText = $row['MenuItem_TotalStocks'] > 0 ? 'AVAILABLE' : 'UNAVAILABLE';
                    ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['MenuItem_Image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['MenuItem_Name']); ?>" 
                             class="card-img-top">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($row['MenuItem_Name']); ?></h3>
                            <p class="card-text"><?php echo htmlspecialchars($row['MenuItem_Description']); ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Stock: <?php echo $row['MenuItem_TotalStocks']; ?> | 
                                    Sold: <?php echo $row['MenuItem_TotalSold']; ?>
                                </small>
                            </p>
                            <button class="btn <?php echo $availabilityClass; ?> status">
                                <?php echo $availabilityText; ?>
                            </button>
                            <div class="actions">
                                <button class="btn btn-warning action-btn" 
                                        onclick="editMenuItem(<?php echo $row['MenuItem_ID']; ?>)">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-danger action-btn" 
                                        onclick="deleteMenuItem(<?php echo $row['MenuItem_ID']; ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
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

  <!-- Bootstrap JS -->
  <script src="Css-admin/bootstrap.bundle.min.js"></script>
  
  <script>
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
      if (confirm('Are you sure you want to delete this menu item?')) {
          window.location.href = 'delete_menu_item.php?id=' + menuItemId;
      }
  }

  function addMenuItem() {
      openModal('add_menu_item.php');
  }

  // Close modal when clicking outside
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
          closeModal();
      }
  });

  // Close modal with escape key
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          closeModal();
      }
  });
  </script>
</body>
</html>
