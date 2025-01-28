<?php
// feedback.php: Display feedback and rating chart
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch username for the sidebar
// $username = $_SESSION['Staff_Username'];

// Database connection
$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "kiosk_ordering_system_db";

$conn = new mysqli($servername, $username_db, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback data
$feedbacks = [];
$ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$sql = "SELECT Feedback_CustomerName, Feedback_Rating, Feedback_Comments FROM feedback";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
        $ratings[$row['Feedback_Rating']]++;
    }
}
$conn->close();

// Calculate rating chart data
$totalRatings = array_sum($ratings);
$ratingChart = array_map(function($count) use ($totalRatings) {
    return $totalRatings > 0 ? round(($count / $totalRatings) * 100, 2) : 0;
}, $ratings);
?>

<?php
// session_start();

// // Check if user is not logged in
// if(!isset($_SESSION["user_id"])) {
//     header("Location: login.php");
//     exit();
// }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedbackscreen</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <!-- Add Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/sidebar.css">
        <link rel="stylesheet" href="Css-admin/order.css">
        <link rel="stylesheet" href="Css-admin/search_order.css">
        <link rel="stylesheet" href="Css-admin/admin-modal.css">
        <link rel="stylesheet" href="Css-admin/reports.css">
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
  
</head>
<body>
  <div class="wrapper">
        <?php 
        require_once 'includes/sidebar.php';
        renderSidebar('feedback');
        ?>
    <div class="main-content">
    <h1>Customer Feedback</h1>
    <canvas id="ratingChart"></canvas> <!-- Chart with margin -->

    <!-- Feedback List -->
    <div class="feedback-container">
        <div class="feedback-list">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-item">
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($feedback['Feedback_CustomerName']); ?></p>
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($feedback['Feedback_Rating']); ?>/5</p>
                    <p><?php echo htmlspecialchars($feedback['Feedback_Comments']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    <script>
    const ctx = document.getElementById('ratingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
            datasets: [{
                label: 'Rating',
                data: <?php echo json_encode(array_values($ratingChart)); ?>,
                backgroundColor: '#36A2EB' // Blue color for all bars
            }]
        },
        options: {
            indexAxis: 'y', // Make the bar chart horizontal
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
