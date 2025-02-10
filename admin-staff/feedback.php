<?php
// feedback.php: Display feedback and rating chart
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include access control
require_once 'check_admin_access.php';

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
$sql = "SELECT f.Feedback_CustomerName, f.Feedback_Rating, f.Feedback_Comments, f.Feedback_DateTime, 
        o.Order_ID, o.Order_TicketNumber 
        FROM feedback f 
        LEFT JOIN `order` o ON f.Order_ID = o.Order_ID
        ORDER BY f.Feedback_DateTime DESC";
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

// Calculate average rating
$totalScore = 0;
foreach ($ratings as $rating => $count) {
    $totalScore += ($rating * $count);
}
$averageRating = $totalRatings > 0 ? round($totalScore / $totalRatings, 1) : 0;
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
  <title>Customer Feedback - SINCO CAFE</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <!-- Add Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/sidebar.css">
        <link rel="stylesheet" href="Css-admin/order.css">
        <link rel="stylesheet" href="Css-admin/search_order.css">
        <link rel="stylesheet" href="Css-admin/admin-modal.css">
        <link rel="stylesheet" href="Css-admin/reports.css">
        <link rel="stylesheet" href="Css-admin/feedback.css">
        <link rel="stylesheet" href="Javascript-admin/auto-refresh.js">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
        <style>
            .feedback-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                color: #666;
                font-size: 0.9em;
            }
            .feedback-datetime {
                margin-top: 10px;
                color: #666;
                font-size: 0.9em;
                text-align: right;
            }
            .feedback-item {
                background: #fff;
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: transform 0.2s ease;
            }
            .feedback-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .feedback-item p {
                margin: 5px 0;
            }
            .feedback-container {
                padding: 20px;
                max-width: 1200px;
                margin: 0 auto;
            }
            .feedback-list {
                display: grid;
                gap: 20px;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
            .rating {
                color: #ffc107;
                margin: 10px 0;
            }
            .rating i {
                margin-right: 2px;
            }
            .rating span {
                color: #666;
                margin-left: 5px;
            }
            .feedback-comments {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                margin: 10px 0;
            }
            .stats-container {
                max-width: 100%;
                margin: 0 auto 20px;
                padding: 0 10px;
            }
            .feedback-summary {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin: 15px 0;
                padding: 12px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .feedback-summary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .summary-item {
                text-align: center;
                padding: 0 12px;
                flex: 1;
                min-width: 100px;
            }
            .summary-item h3 {
                font-size: clamp(16px, 2.5vw, 20px);
                margin: 0;
                color: #333;
                white-space: nowrap;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
            }
            .summary-item h3 i {
                color: #ffc107;
                font-size: 0.8em;
            }
            .summary-item p {
                margin: 3px 0 0;
                color: #666;
                font-size: clamp(0.75em, 1.8vw, 0.85em);
            }
            .chart-container {
                background: linear-gradient(145deg, #383838, #2a2a2a);
                padding: 12px;
                border-radius: 8px;
                box-shadow: 0 3px 6px rgba(0,0,0,0.3);
                height: 512px;
                width: 100%;
            }
            @media (max-width: 576px) {
                .stats-container {
                    padding: 0 8px;
                    margin-bottom: 15px;
                }
                .feedback-summary {
                    padding: 8px;
                    gap: 8px;
                    margin: 10px 0;
                }
                .summary-item {
                    padding: 0 8px;
                }
                .chart-container {
                    height: 130px;
                    padding: 8px;
                }
            }
            h1 {
                margin: clamp(15px, 3vw, 25px) 0;
                font-size: clamp(24px, 4vw, 32px);
                text-align: center;
            }
        </style>
</head>
<body>
  <div class="wrapper">
        <?php 
        require_once 'includes/sidebar.php';
        renderSidebar('feedback');
        ?>
    <div class="main-content">
    <h1>Customer Feedback</h1>
    
    <div class="stats-container">
        <div class="feedback-summary">
            <div class="summary-item">
                <h3><?php echo $totalRatings; ?></h3>
                <p>Total Feedbacks</p>
            </div>
            <div class="summary-item">
                <h3><?php echo $averageRating; ?> <i class="fas fa-star"></i></h3>
                <p>Average Rating</p>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="ratingChart"></canvas>
        </div>
    </div>

    <!-- Feedback List -->
    <div class="feedback-container">
        <div class="feedback-list">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-item">
                    <div class="feedback-header">
                        <p><strong>Order ID:</strong> #<?php echo str_pad(htmlspecialchars($feedback['Order_ID'] ?? ''), 8, '0', STR_PAD_LEFT); ?></p>
                        <p><strong>Ticket #:</strong> <?php echo htmlspecialchars($feedback['Order_TicketNumber'] ?? 'N/A'); ?></p>
                    </div>
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($feedback['Feedback_CustomerName']); ?></p>
                    <p class="rating">
                        <strong>Rating:</strong> 
                        <?php 
                        $rating = (int)$feedback['Feedback_Rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                        <span>(<?php echo $rating; ?>/5)</span>
                    </p>
                    <div class="feedback-comments">
                        <strong>Comments:</strong><br>
                        <?php echo nl2br(htmlspecialchars($feedback['Feedback_Comments'])); ?>
                    </div>
                    <p class="feedback-datetime"><strong>Date/Time:</strong> <?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($feedback['Feedback_DateTime']))); ?></p>
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
                label: 'Rating Distribution',
                data: <?php echo json_encode(array_values($ratingChart)); ?>,
                backgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    
                    if (!chartArea) {
                        return null;
                    }
                    
                    const colors = [
                        ['#ff6b6b', '#ff4444'],  // Red gradient
                        ['#ffd93d', '#ffc107'],  // Yellow gradient
                        ['#6c757d', '#495057'],  // Gray gradient
                        ['#4dabf7', '#339af0'],  // Blue gradient
                        ['#51cf66', '#40c057']   // Green gradient
                    ];
                    
                    const index = context.dataIndex;
                    const gradient = ctx.createLinearGradient(0, 0, chartArea.right, 0);
                    gradient.addColorStop(0, colors[index][0]);
                    gradient.addColorStop(1, colors[index][1]);
                    
                    return gradient;
                },
                hoverBackgroundColor: [
                    '#ff4444',  // Darker red
                    '#ffc107',  // Darker yellow
                    '#495057',  // Darker gray
                    '#339af0',  // Darker blue
                    '#40c057'   // Darker green
                ]
            }]
        },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                layout: {
                    padding: {
                        left: 5,
                        right: 10,
                        top: 5,
                        bottom: 5
                    }
                },
                color: '#fff',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(40, 40, 40, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 8,
                        callbacks: {
                            label: function(context) {
                                return context.raw.toFixed(1) + '% of ratings';
                            }
                        },
                        titleFont: {
                            size: 10,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 10
                        },
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                size: function(context) {
                                    const width = context.chart.width;
                                    return width < 400 ? 9 : 11;
                                }
                            },
                            color: '#fff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: function(context) {
                                    const width = context.chart.width;
                                    return width < 400 ? 9 : 11;
                                }
                            },
                            color: '#fff'
                        }
                    }
                }
            }
    });
</script>

</body>
</html>
