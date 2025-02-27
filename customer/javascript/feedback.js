$(document).ready(function () {
    let selectedRating = 0;
    const ratingTexts = [
        "",
        "Poor - We're sorry for your experience",
        "Fair - We'll try to do better",
        "Good - Thank you for your feedback",
        "Very Good - We're glad you enjoyed it",
        "Excellent - We're thrilled you loved it!"
    ];

    // Star selection with animation
    $('.stars .fa-star').on('click', function () {
        selectedRating = $(this).data('value');
        updateStars(selectedRating);
        
        // Add pulse animation to selected stars
        $('.stars .fa-star.active').css('animation', 'none');
        setTimeout(function() {
            $('.stars .fa-star.active').css('animation', 'starPulse 0.5s ease');
        }, 10);
        
        // Update rating text
        $('#rating-text').text(ratingTexts[selectedRating]);
    });

    // Function to update stars based on rating
    function updateStars(rating) {
        $('.stars .fa-star').each(function () {
            $(this).toggleClass('active', $(this).data('value') <= rating);
        });
    }

    // Hover effect on stars with smooth transitions
    $('.stars .fa-star').hover(
        function () {
            const hoverRating = $(this).data('value');
            
            // Only apply hover effect if no rating is selected or on stars above the selected rating
            if (selectedRating === 0) {
                $('.stars .fa-star').each(function () {
                    $(this).toggleClass('hover', $(this).data('value') <= hoverRating);
                });
            } else {
                $('.stars .fa-star').each(function () {
                    if ($(this).data('value') > selectedRating && $(this).data('value') <= hoverRating) {
                        $(this).addClass('hover');
                    }
                });
            }
            
            // Show temporary rating text on hover
            if (selectedRating === 0) {
                $('#rating-text').text(ratingTexts[hoverRating]);
            }
        },
        function () {
            $('.stars .fa-star').removeClass('hover');
            
            // Restore selected rating text or clear if none selected
            $('#rating-text').text(selectedRating > 0 ? ratingTexts[selectedRating] : '');
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

    // Form validation and submission
    $('#submit-feedback').on('click', function () {
        const customerName = $('#customer-name').val().trim();
        const feedbackText = $('#feedback-text').val().trim();

        if (selectedRating === 0) {
            showAlert('Please select a star rating.');
            return;
        }

        if (!feedbackText) {
            showAlert('Please enter your feedback.');
            return;
        }

        // Show loading spinner
        $('#spinner').show();
        $('#submit-feedback').prop('disabled', true);

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
                // Hide spinner
                $('#spinner').hide();
                $('#submit-feedback').prop('disabled', false);
                
                try {
                    const result = JSON.parse(response.trim());
                    if (result.status === 'success') {
                        $('#thank-you-modal').modal('show');
                        // Reset form
                        $('#customer-name, #feedback-text').val('');
                        $('.stars .fa-star').removeClass('active');
                        $('#rating-text').text('');
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
                // Hide spinner
                $('#spinner').hide();
                $('#submit-feedback').prop('disabled', false);
                showAlert('Request failed: ' + error);
            }
        });
    });

    // Back button logic
    $('#back-button').on('click', function () {
        window.location.href = 'orderstatus.php';
    });

    // Thank you modal redirect
    $('#thank-you-modal').on('hidden.bs.modal', function () {
        // Redirect to orderstatus.php after closing modal
        window.location.href = 'orderstatus.php';
    });

    // Floating label effect for input fields
    $('.input-group input, .input-group textarea').on('focus blur', function() {
        $(this).siblings('label').toggleClass('active', $(this).val().trim() !== '' || $(this).is(':focus'));
    }).each(function() {
        // Initialize labels for pre-filled inputs
        $(this).siblings('label').toggleClass('active', $(this).val().trim() !== '');
    });
});
