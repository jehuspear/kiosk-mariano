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
      <ul class="nav">
        <li><a href="login.php"><i class="fa-solid fa-user"></i> Login</a></li>
        <li class="active"><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
        <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
        <li><a href="orderlist.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
        <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
        <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Reports</a></li>
        <li><a href="decodingscreen.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
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
        <!-- Menu Card -->
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
              <button id="editButton" class="btn btn-warning action-btn"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-danger action-btn"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </div>

         <!-- Menu Card -->
      <div class="card">
        <img src="Images/salted-caramel.jpg" alt="Salted Caramel" class="card-img-top">
        <div class="card-body">
          <h3 class="card-title">Salted Caramel</h3>
          <button class="btn btn-success status available">AVAILABLE</button>
          <div class="actions">
            <button id="editButton" class="btn btn-warning action-btn">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="btn btn-danger action-btn">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
   

        <!-- Add Menu Card -->
        <div class="card add-menu-card ">
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

  <script>
    // Add click event listener to the edit button
    document.getElementById('editButton').addEventListener('click', function () {
      // Use Bootstrap's modal API to show the modal
      const editModal = new bootstrap.Modal(document.getElementById('editModal'));
      editModal.show();
    });
  </script>
</body>
</html>


