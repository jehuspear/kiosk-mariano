<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Management Screen</title>
  
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="Css-admin/decodingscreen.css">
  
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
        <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
        <li class="active"><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
        <li><a href="orderlist.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
        <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
        <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
        <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Reports</a></li>
        <li><a href="decodingscreen.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
      </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
      <div class="form-container">
        <h1>Please Enter E-Ticket <br> Code</h1>
        <input type="text" placeholder="Enter Code">
        <button>OK</button>
      </div>
    </div>
  </div>
</body>
</html>
