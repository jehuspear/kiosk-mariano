<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include 'database_admin.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Management </title>
  
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="Css-admin/home.css">
  <link rel="stylesheet" href="Css-admin/home-screen.css">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
 

    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo">
        <h2>SINCO CAFE</h2>
      </div>
      <ul class="nav">
        <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i> <span>Logout</span></a></li>
        <li class="active"><a href="home.php"><i class="fa-solid fa-user"></i> <span>Home</span></a></li>
        <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> <span>Menu</span></a></li>
        <!-- <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> <span>E-ticket</span></a></li> -->
        <li><a href="pending-orders.php"><i class="fa-solid fa-mug-hot"></i> <span>Pending</span></a></li>
        <li><a href="preparing-orders.php"><i class="fa-solid fa-sort"></i> <span>Order list</span></a></li>
        <li><a href="completed-orders.php"><i class="fa-solid fa-check-to-slot"></i> <span>Completed</span></a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> <span>Dashboard</span></a></li>
        <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> <span>Feedback</span></a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> <span>History</span></a></li>
      </ul>
  </div>

  <div class="logo-container">
            <img src="Images/logo/logo.png" alt="Mariano Cafe Logo" class="logo img-fluid">
            <p class="header-tagline">Where Good Coffee Starts</p>
        </div>



  <!-- Bootstrap JS -->
  <script src="Css-admin/bootstrap.bundle.min.js"></script>


</body>
</html>
