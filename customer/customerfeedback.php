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
    <title>Feedback - SINCO CAFE</title>
    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Local Bootstrap JS (includes Popper.js) -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .content-screen {
            display: none;
            text-align: center;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .active-screen {
            display: block;
        }

        .logo img {
            width: 200px;
            margin-bottom: 50px;
            margin-top: -50px;
        }

        .name-input,
        .feedback-textarea {
            width: 80%;
            margin: 10px auto;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            display: block;
        }

        .name-input {
            background-color: #fff;
            color: #000;
        }

        .feedback-textarea {
            height: 100px;
            background-color: #f8f9fa;
            color: #000;
            resize: none;
        }

        .stars i {
            font-size: 2rem;
            margin: 0 5px;
            color: #fff;
            cursor: pointer;
        }

        .stars i.active {
            color: #ffc107;
        }

        .stars i.hover {
            color: #ddd;
        }

        .btn {
            width: 80%;
            margin: 10px auto;
            padding: 12px;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: #28a745;
            border: none;
            color: white;
            border: none;
            padding: 12px;
            /* border-radius: 25px; */
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            text-align: center;
        }

        .btn-secondary {
            background-color: #dc3545;
            border: none;
        }

        .btn:hover {
            opacity: 0.9;
            cursor: pointer;
            background-color: #218838;
            color: white;
            text-decoration: none;
        }

        /* Modal Styling */
        .modal-content {
            background-color: #343a40;
            color: white;
        }

        .modal-header {
            border-bottom: 1px solid #ccc;
        }

        .modal-footer {
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <!-- First Screen -->
    <div id="screen1" class="content-screen active-screen">
        <div class="logo">
            <img src="resources/images/logo.png" alt="SINCO CAFE Logo">
        </div>
        <div class="stars" id="star-rating">
            <i class="fas fa-star" data-value="1"></i>
            <i class="fas fa-star" data-value="2"></i>
            <i class="fas fa-star" data-value="3"></i>
            <i class="fas fa-star" data-value="4"></i>
            <i class="fas fa-star" data-value="5"></i>
        </div>
        <input type="text" class="name-input" id="customer-name" placeholder="Enter Name Here (Optional)">
        <button class="btn btn-primary" id="next-button">Next</button>
        <div class="">
        <a href="orderstatus.php" class="btn btn-primary">Back to your Order Details</a>
    </div>
    </div>

    <!-- Second Screen -->
    <div id="screen2" class="content-screen">
        <div class="logo">
            <img src="resources/images/logo.png" alt="SINCO CAFE Logo">
        </div>
        <h2>Rate your experience with us</h2>
        <div class="stars" id="review-stars"></div>
        <textarea class="feedback-textarea" id="feedback-text" placeholder="Share your feedback with us!"></textarea>
        <button class="btn btn-primary" id="submit-feedback">Submit</button>
        <button class="btn btn-secondary" id="back-button">Cancel</button>
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


<script>
    $(document).ready(function () {
        let selectedRating = 0;

        // First screen star selection
        $('#star-rating .fa-star').on('click', function () {
            selectedRating = $(this).data('value');
            $('#star-rating .fa-star').each(function () {
                $(this).toggleClass('active', $(this).data('value') <= selectedRating);
            });
        });

        // Hover effect on stars
        $('#star-rating .fa-star').hover(
            function () {
                const hoverRating = $(this).data('value');
                $('#star-rating .fa-star').each(function () {
                    $(this).toggleClass('hover', $(this).data('value') <= hoverRating);
                });
            },
            function () {
                $('#star-rating .fa-star').removeClass('hover');
            }
        );

        // Function to show alert modal with optional title and callback
        function showAlert(message, title = 'Alert', callback = null) {
            $('#alertModalLabel').text(title);
            $('#alert-message').text(message);
            const modal = $('#alert-modal');
            
            if (callback) {
                modal.one('hidden.bs.modal', callback);
            }
            
            modal.modal('show');
        }

        // Check session status periodically
        function checkSession() {
            $.get('check_session.php', function(response) {
                if (!response.hasTicket) {
                    if (response.message) {
                        showAlert(response.message, 'Feedback Already Submitted', function() {
                            window.location.href = 'orderstatus.php';
                        });
                    } else {
                        showAlert('Your session has expired. You will be redirected to your Order Details Page.', 'Session Expired', function() {
                            window.location.href = 'orderstatus.php';
                        });
                    }
                }
            });
        }

        // Initial session check
        checkSession();

        // Check session every 30 seconds
        setInterval(checkSession, 30000);

        // Move to second screen
        $('#next-button').on('click', function () {
            const customerName = $('#customer-name').val().trim();
            if (selectedRating === 0) {
                showAlert('Please select a star rating.');
            } else {
                // Proceed to next screen even if name is empty
                $('#review-stars').html('');
                for (let i = 1; i <= 5; i++) {
                    $('#review-stars').append(
                        `<i class="fas fa-star ${i <= selectedRating ? 'active' : ''}" data-value="${i}"></i>`
                    );
                }
                $('#screen1').removeClass('active-screen');
                $('#screen2').addClass('active-screen');
            }
        });

        // Submit feedback (AJAX call to save feedback into the database)
        $('#submit-feedback').on('click', function () {
            const feedbackText = $('#feedback-text').val().trim();
            const customerName = $('#customer-name').val().trim();

            if (!feedbackText) {
                showAlert('Please enter your feedback.');
                return;
            }

            // Send data to backend via AJAX
            $.ajax({
                url: 'customerfeedback.php',
                method: 'POST',
                data: {
                    Feedback_CustomerName: customerName,
                    Feedback_Rating: selectedRating,
                    Feedback_Comments: feedbackText
                },
                success: function (response) {
    try {
        const result = JSON.parse(response.trim());
        if (result.status === 'success') {
            $('#thank-you-modal').modal('show');
            // Reset form
            $('#customer-name, #feedback-text').val('');
            $('#star-rating .fa-star').removeClass('active');
            selectedRating = 0;
        } else {
            showAlert('Error: ' + result.message);
        }
    } catch (e) {
        console.error('Error:', e);
        showAlert('An unexpected error occurred.');
    }
},
error: function (xhr, status, error) {
    showAlert('Request failed: ' + error);
}
            });
        });

        // Back button logic
        $('#back-button').on('click', function () {
            $('#screen2').removeClass('active-screen');
            $('#screen1').addClass('active-screen');
        });

       // In customerfeedback.php's script section, replace this:
$('#thank-you-modal').on('hidden.bs.modal', function () {
    // When the modal is closed, go back to the first screen
    $('#screen2').removeClass('active-screen');
    $('#screen1').addClass('active-screen');

    // Reset the name input, rating, and other fields to their defaults
    $('#customer-name').val(''); 
    $('#feedback-text').val(''); 
    $('#star-rating .fa-star').removeClass('active');
    selectedRating = 0;
});

    // With this redirect code:
        $('#thank-you-modal').on('hidden.bs.modal', function () {
    // Redirect to orderstatus.php after closing modal
        window.location.href = 'orderstatus.php';
    });
 });
</script>
</body>
</html>
