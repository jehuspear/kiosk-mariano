<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedbackscreen</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <!-- Add Bootstrap CSS -->
 <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
 <link rel="stylesheet" href="Css-admin/feedback.css">
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
  
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
            <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
            <li><a href="decodingscreen.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
            <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
            <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
            <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Reports</a></li>
            <li class="active"><a href="feedback.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
            <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
    </div>


      <!-- Main Content -->
    <div class="main-content">
      <!-- Rating Count Section -->
      <div class="ratings-container">
        <h3 class="section-title">Rating Count</h3>
        <canvas id="ratingChart"></canvas>
      </div>
<!-- Script for Chart -->
    <script>
       const ctx = document.getElementById('ratingChart').getContext('2d');
      new Chart(ctx, {
    type: 'bar', // Horizontal Bar chart type
    data: {
      labels: ['5★', '4★', '3★', '2★', '1★'], // Rating labels
      datasets: [{
        label: 'Rating Count',
        data: [90, 77, 60, 40, 20], // Rating data
        backgroundColor: ['#0af', '#0af', '#0af', '#0af', '#0af'], // Bar colors
        borderRadius: 5,
      }]
    },
    options: {
      responsive: true,
      indexAxis: 'y', // This makes it horizontal
      plugins: {
        legend: { display: false }, // Hide legend
      },
      scales: {
        x: {
          ticks: { color: '#fff' }, // X-axis labels color
          grid: { color: '#444' },  // X-axis grid lines color
        },
        y: {
          ticks: { color: '#fff' }, // Y-axis labels color
          grid: { display: false }, // Hide Y-axis grid lines
        }
      }
    }
  });
</script>

      <!-- Feedback Section -->
      <div class="feedback-container">
        <h3 class="section-title">Feedbacks</h3>
        <div class="feedback-item">Super Delicious! OMG can't wait to go back and drink some more</div>
        <div class="feedback-item">Lasang sabon daw yung kape niyo</div>
      </div>
    </div>
  </div>
</body>
</html>
