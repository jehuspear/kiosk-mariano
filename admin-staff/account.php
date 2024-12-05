<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account</title>
  
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="Css-admin/account.css">
  
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
        <li class="active"><a href="login.php"><i class="fa-solid fa-user"></i> Clarence</a></li>
        <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
        <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
        <li><a href="orderlist.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
        <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
        <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Dashboard</a></li>
        <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
      </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="form-container">
          <h1>Welcome, Clarence!</h1>
          <!-- Add the id 'signOutBtn' to the button -->
          <button id="signOutBtn">Sign Out?</button>
        </div>
      </div>    

  <!-- Sign-out Modal -->
  <div id="signOutModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeModal">&times;</span>
      <h2>Are you sure you want to sign out?</h2>
      <div class="modal-footer">
        <button id="yesBtn">Yes</button>
        <button id="noBtn" class="no">No</button>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS -->
  <script src="Css-admin/bootstrap.bundle.min.js"></script>
  <script src="Javascript-admin/account.js"></script>
</body>
</html>