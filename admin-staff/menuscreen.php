<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Management Screen</title>
  <!-- Add Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="menuscreen.css">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo">
        <h2>SINCO CAFE</h2>
      </div>
      <ul class="nav flex-column">
        <li><i class="fa-solid fa-user"></i> Login</li>
        <li class="active"><i class="fa-solid fa-book"></i> Menu</li>
        <li><i class="fa-solid fa-ticket"></i> E-Ticket</li>
        <li><i class="fa-solid fa-sort"></i> Order Lists</li>
        <li><i class="fa-solid fa-mug-hot"></i> Orders</li>
        <li><i class="fa-solid fa-check-to-slot"></i> Completed</li>
        <li><i class="fa-solid fa-newspaper"></i> Reports</li>
        <li><i class="fa-regular fa-comment"></i> Feedback</li>
        <li><i class="fa-solid fa-clock-rotate-left"></i> History</li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header Menu -->
      <div class="header-menu">
        <div class="menu-item active">
          <i class="fa-solid fa-coffee"></i>
          <p>Traditional Coffee</p>
        </div>
        <div class="menu-item">
          <i class="fa-solid fa-mug-hot"></i>
          <p>Coffee</p>
        </div>
        <div class="menu-item">
          <i class="fa-solid fa-wine-glass"></i>
          <p>Non-Coffee</p>
        </div>
        <div class="menu-item">
          <i class="fa-solid fa-cocktail"></i>
          <p>Mocktail</p>
        </div>
        <div class="menu-item">
          <i class="fa-solid fa-cookie"></i>
          <p>Pastries</p>
        </div>
        <div class="menu-item">
          <i class="fa-solid fa-pizza-slice"></i>
          <p>Snacks</p>
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
      <div class="menu-cards d-flex flex-wrap">
        <div class="card">
          <img src="Images/salted-caramel.jpg" alt="Salted Caramel" class="card-img-top">
          <div class="card-body">
            <h3 class="card-title">Salted Caramel</h3>
            <button class="btn btn-success status available">AVAILABLE</button>
            <div class="actions">
              <button class="btn btn-warning action-btn"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-danger action-btn"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </div>

        <div class="card">
          <img src="Images/cafemocha.jpg" alt="Cafe Mocha" class="card-img-top">
          <div class="card-body">
            <h3 class="card-title">Cafe Mocha</h3>
            <button class="btn btn-danger status unavailable">UNAVAILABLE</button>
            <div class="actions">
              <button class="btn btn-warning action-btn"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-danger action-btn"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </div>

        <div class="card">
          <img src="Images/Spanish-latte.jpg" alt="Spanish Latte" class="card-img-top">
          <div class="card-body">
            <h3 class="card-title">Spanish Latte</h3>
            <button class="btn btn-success status available">AVAILABLE</button>
            <div class="actions">
              <button class="btn btn-warning action-btn"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-danger action-btn"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </div>

        <!-- Add Menu Card -->
        <div class="card add-menu-card">
          <div class="add-menu-container">
            <button class="btn btn-outline-primary add-menu-btn">
              <i class="fa-solid fa-plus"></i>
            </button>
            <span class="add-menu-text">Add Menu</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Bootstrap JS (Optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>