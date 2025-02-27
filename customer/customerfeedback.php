<?php
// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "kiosk_ordering_system_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle empty name as empty string (since NOT NULL constraint)
    $Feedback_CustomerName = !empty($_POST['Feedback_CustomerName']) 
        ? $conn->real_escape_string($_POST['Feedback_CustomerName'])
        : 'Anonymous'; 

    $Feedback_Rating = intval($_POST['Feedback_Rating']);
    $Feedback_Comments = $conn->real_escape_string($_POST['Feedback_Comments']);
    
    // Start session to get ticket number
    session_start();
    
    // Get Order_ID based on ticket number from session
    $Order_ID = null;
    if (isset($_SESSION['ticket_number'])) {
        $ticketNumber = $_SESSION['ticket_number'];
        
        // Get today's date range in Manila time
        date_default_timezone_set('Asia/Manila');
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');
        
        $orderQuery = "SELECT Order_ID, Order_Status FROM `order` 
                      WHERE Order_TicketNumber = ? 
                      AND Order_DateTime BETWEEN ? AND ?";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("iss", $ticketNumber, $today_start, $today_end);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $orderRow = $result->fetch_assoc();
            $Order_ID = $orderRow['Order_ID'];
            
            // Check if order is completed
            if ($orderRow['Order_Status'] !== 'Completed') {
                echo json_encode(["status" => "error", "message" => "Feedback can only be submitted for completed orders"]);
                exit();
            }
            
            // Check if feedback already exists for this order
            $feedbackCheck = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE Order_ID = ?");
            $feedbackCheck->bind_param("i", $Order_ID);
            $feedbackCheck->execute();
            $feedbackResult = $feedbackCheck->get_result();
            $feedbackCount = $feedbackResult->fetch_assoc()['count'];
            $feedbackCheck->close();
            
            if ($feedbackCount > 0) {
                echo json_encode(["status" => "error", "message" => "Feedback has already been submitted for this order"]);
                exit();
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Order not found for this ticket number"]);
            exit();
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "No ticket number found in session"]);
        exit();
    }

    // Set timezone and get current datetime
    date_default_timezone_set('Asia/Manila');
    $currentDateTime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO feedback (Order_ID, Feedback_CustomerName, Feedback_DateTime, Feedback_Rating, Feedback_Comments) 
            VALUES (?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issis", $Order_ID, $Feedback_CustomerName, $currentDateTime, $Feedback_Rating, $Feedback_Comments);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Feedback submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>


<?php
// Start session at the beginning to check for ticket number
session_start();

// Check if user has a valid ticket number
if (!isset($_SESSION['ticket_number'])) {
    header('Location: orderstatus.php');
    exit();
}

// Check if feedback already exists for this order
$ticketNumber = $_SESSION['ticket_number'];
date_default_timezone_set('Asia/Manila');
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

$orderQuery = "SELECT o.Order_ID FROM `order` o 
              WHERE o.Order_TicketNumber = ? 
              AND o.Order_DateTime BETWEEN ? AND ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("iss", $ticketNumber, $today_start, $today_end);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $orderRow = $result->fetch_assoc();
    $Order_ID = $orderRow['Order_ID'];
    
    // Check for existing feedback
    $feedbackCheck = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE Order_ID = ?");
    $feedbackCheck->bind_param("i", $Order_ID);
    $feedbackCheck->execute();
    $feedbackResult = $feedbackCheck->get_result();
    $feedbackCount = $feedbackResult->fetch_assoc()['count'];
    $feedbackCheck->close();
    
    if ($feedbackCount > 0) {
        header('Location: orderstatus.php');
        exit();
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - White House CAFE</title>

    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="resources/favicon/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="resources/favicon/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="resources/favicon/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="resources/favicon/favicon_io/site.webmanifest">

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Local Bootstrap JS (includes Popper.js) -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/feedback.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="feedback-container">
        <div class="logo">
            <img src="resources/images/logo.png" alt="White House CAFE Logo">
        </div>
        
        <h2 class="feedback-title">How was your experience?</h2>
        
        <div class="stars-container">
            <div class="stars" id="star-rating">
                <i class="fas fa-star" data-value="1"></i>
                <i class="fas fa-star" data-value="2"></i>
                <i class="fas fa-star" data-value="3"></i>
                <i class="fas fa-star" data-value="4"></i>
                <i class="fas fa-star" data-value="5"></i>
            </div>
            <div class="rating-text" id="rating-text"></div>
        </div>
        
        <div class="input-group">
            <input type="text" id="customer-name" placeholder=" ">
            <label for="customer-name">Your Name (Optional)</label>
        </div>
        
        <div class="input-group">
            <textarea id="feedback-text" placeholder=" "></textarea>
            <label for="feedback-text">Share your feedback with us</label>
        </div>
        
        <div class="spinner" id="spinner"></div>
        
        <div class="btn-container">
            <button class="btn btn-primary" id="submit-feedback">Submit Feedback</button>
            <button class="btn btn-secondary" id="back-button">Back</button>
        </div>
    </div>

    <!-- Alert Modals -->
    <div id="alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="alert-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You Modal -->
    <div id="thank-you-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="thankYouModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="thankYouModalLabel">Thank You!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Thank you for your feedback for Order #<?php echo htmlspecialchars($_SESSION['ticket_number']); ?>!</p>
                    <p>We appreciate your time in helping us improve our service.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script src="javascript/feedback.js"></script>
</body>
</html>
