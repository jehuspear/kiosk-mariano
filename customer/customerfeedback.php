<?php
// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "kiosk_ordering_system_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $rating = intval($_POST['rating']);
    $feedback = $conn->real_escape_string($_POST['feedback']);

    // Insert data into the database
    $sql = "INSERT INTO customerfeedback (customer_name, rating, feedback) VALUES ('$customer_name', $rating, '$feedback')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Feedback submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

$conn->close();
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
        }

        .btn-secondary {
            background-color: #dc3545;
            border: none;
        }

        .btn:hover {
            opacity: 0.9;
            cursor: pointer;
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
        <input type="text" class="name-input" id="customer-name" placeholder="Enter Name Here">
        <button class="btn btn-primary" id="next-button">Next</button>
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

   <!-- Thank You Modal -->
<div id="thank-you-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="thankYouModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="thankYouModalLabel">Thank You!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Your feedback has been submitted.</p>
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

    // Move to second screen
    $('#next-button').on('click', function () {
        const customerName = $('#customer-name').val().trim();
        if (selectedRating === 0) {
            alert('Please select a star rating.');
        } else if (!customerName) {
            alert('Please enter your name.');
        } else {
            $('#review-stars').html(''); // Clear previous stars
            for (let i = 1; i <= 5; i++) {
                $('#review-stars').append(
                    `<i class="fas fa-star ${i <= selectedRating ? 'active' : ''}" data-value="${i}"></i>`
                );
            }

            $('#rating-message').text(`You rated us ${selectedRating} star${selectedRating > 1 ? 's' : ''}!`);
            $('#screen1').removeClass('active-screen');
            $('#screen2').addClass('active-screen');
        }
    });

    // Submit feedback (AJAX call to save feedback into the database)
    $('#submit-feedback').on('click', function () {
        const feedbackText = $('#feedback-text').val().trim();
        const customerName = $('#customer-name').val().trim();

        if (!feedbackText) {
            alert('Please enter your feedback.');
            return;
        }

        // Send data to backend via AJAX
        $.ajax({
            url: 'customerfeedback.php',
            method: 'POST',
            data: {
                customer_name: customerName,
                rating: selectedRating,
                feedback: feedbackText
            },
            success: function (response) {
    try {
        // Log the raw response for debugging (optional, you can remove this in production)
        console.log("Raw response:", response);  

        // Ensure the response is in JSON format and parse it
        const result = JSON.parse(response.trim());

        // If the response is successful, show the thank you modal
        if (result.status === 'success') {
            // Show thank-you modal
            $('#thank-you-modal').modal('show');
            
            // Reset inputs and UI
            $('#customer-name, #feedback-text').val('');
            $('#star-rating .fa-star').removeClass('active');
            selectedRating = 0;
            $('#screen2').removeClass('active-screen');
            $('#screen1').addClass('active-screen');
        } 
    } catch (e) {
        // Error parsing the response, but just show the modal anyway (no alert)
        console.error('Error parsing response:', e); // Log the error to the console (optional)
        $('#thank-you-modal').modal('show'); // Show the modal even if there's an issue
    }
},
    error: function () {
    // Show the modal even if an error occurs during the AJAX request
    $('#thank-you-modal').modal('show'); 
}

    });
    });

  // Back button logic
$('#back-button').on('click', function () {
    $('#screen2').removeClass('active-screen');
    $('#screen1').addClass('active-screen');
});

// Modal close event to go back to the first screen
$('#thank-you-modal').on('hidden.bs.modal', function () {
    // When the modal is closed, go back to the first screen
    $('#screen2').removeClass('active-screen');
    $('#screen1').addClass('active-screen');

    
// Reset the name input, rating, and other fields to their defaults
$('#customer-name').val(''); // Clear the name input
    $('#feedback-text').val(''); // Clear the feedback input
    $('#star-rating .fa-star').removeClass('active'); // Reset stars
    selectedRating = 0; // Reset rating
});
});

</script>

</body>
</html>
