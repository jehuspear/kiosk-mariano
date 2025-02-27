$(document).ready(function() {
    // Initialize date pickers with default values
    const today = new Date();
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(today.getMonth() - 1);
    
    // Format dates for input fields
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set default date values
    $('#start-date').val(formatDate(oneMonthAgo));
    $('#end-date').val(formatDate(today));
    
    // Handle date preset selection
    $('#date-preset').on('change', function() {
        const preset = $(this).val();
        const today = new Date();
        let startDate = new Date();
        
        switch(preset) {
            case 'today':
                startDate = new Date(today);
                break;
            case 'yesterday':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 1);
                break;
            case 'last7days':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 6);
                break;
            case 'last30days':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 29);
                break;
            case 'thisMonth':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case 'lastMonth':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                today.setDate(0); // Last day of previous month
                break;
            case 'custom':
                // Don't change the dates, let user select
                return;
        }
        
        $('#start-date').val(formatDate(startDate));
        $('#end-date').val(formatDate(today));
        
        // If not custom, apply filters automatically
        if (preset !== 'custom') {
            applyFilters();
        }
    });
    
    // Star rating filter selection
    $('.star-option').on('click', function() {
        $(this).toggleClass('selected');
        
        // If any star is selected, set the dropdown to "Custom"
        if ($('.star-option.selected').length > 0) {
            $('#rating-filter').val('custom');
        } else {
            $('#rating-filter').val('all');
        }
    });
    
    // Handle rating dropdown changes
    $('#rating-filter').on('change', function() {
        const value = $(this).val();
        
        // Clear all selected stars
        $('.star-option').removeClass('selected');
        
        // If a specific rating is selected, highlight that star
        if (value !== 'all' && value !== 'custom') {
            $(`.star-option[data-rating="${value}"]`).addClass('selected');
        }
        
        // If not custom, apply filters automatically
        if (value !== 'custom') {
            applyFilters();
        }
    });
    
    // Apply filters button click
    $('#apply-filters').on('click', function() {
        applyFilters();
    });
    
    // Reset filters button click
    $('#reset-filters').on('click', function() {
        resetFilters();
    });
    
    // Function to apply filters
    function applyFilters() {
        // Show loading spinner
        $('.loading-spinner').show();
        
        // Get filter values
        const startDate = $('#start-date').val();
        const endDate = $('#end-date').val();
        const datePreset = $('#date-preset').val();
        
        // Get selected star ratings
        let ratings = [];
        if ($('#rating-filter').val() === 'custom') {
            $('.star-option.selected').each(function() {
                ratings.push($(this).data('rating'));
            });
        } else if ($('#rating-filter').val() !== 'all') {
            ratings.push($('#rating-filter').val());
        }
        
        // Convert ratings to a format PHP can understand
        const ratingsParam = {};
        ratings.forEach((rating, index) => {
            ratingsParam[index] = rating;
        });
        
        // Make AJAX request to get filtered data
        $.ajax({
            url: 'get_filtered_feedback.php',
            type: 'POST',
            data: {
                startDate: startDate,
                endDate: endDate,
                datePreset: datePreset,
                ratings: ratings
            },
            traditional: true, // This helps with array serialization
            dataType: 'json',
            success: function(response) {
                // Hide loading spinner
                $('.loading-spinner').hide();
                
                console.log('Received response:', response);
                
                // Update the feedback list
                updateFeedbackList(response.feedbacks);
                
                // Update the chart
                updateChart(response.ratingChart, response.ratings);
                
                // Update summary statistics
                updateSummary(response.totalRatings, response.averageRating);
                
                // Update active filters display
                updateActiveFilters(startDate, endDate, datePreset, ratings);
            },
            error: function(xhr, status, error) {
                // Hide loading spinner
                $('.loading-spinner').hide();
                
                console.error('Error fetching filtered feedback:', error);
                console.error('Response text:', xhr.responseText);
                
                try {
                    // Try to parse the response as JSON
                    const errorResponse = JSON.parse(xhr.responseText);
                    console.error('Parsed error response:', errorResponse);
                    alert('Error: ' + (errorResponse.error || 'An unknown error occurred'));
                } catch (e) {
                    // If it's not valid JSON, show the raw response
                    console.error('Could not parse error response as JSON');
                    alert('An error occurred while filtering feedback. Please try again.');
                }
            }
        });
    }
    
    // Function to reset filters
    function resetFilters() {
        // Reset date filters
        const today = new Date();
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1);
        
        $('#start-date').val(formatDate(oneMonthAgo));
        $('#end-date').val(formatDate(today));
        $('#date-preset').val('last30days');
        
        // Reset rating filters
        $('#rating-filter').val('all');
        $('.star-option').removeClass('selected');
        
        // Clear active filters
        $('.active-filters').empty();
        
        // Apply reset filters
        applyFilters();
    }
    
    // Function to update the feedback list
    function updateFeedbackList(feedbacks) {
        const feedbackList = $('.feedback-list');
        feedbackList.empty();
        
        if (feedbacks.length === 0) {
            feedbackList.html(`
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p>No feedback found matching your filters.</p>
                </div>
            `);
            return;
        }
        
        // Add each feedback item to the list
        feedbacks.forEach(function(feedback) {
            const stars = generateStarRating(feedback.Feedback_Rating);
            const formattedDate = new Date(feedback.Feedback_DateTime).toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
            
            const feedbackItem = `
                <div class="feedback-item">
                    <div class="feedback-header">
                        <p><strong>Order ID:</strong> #${String(feedback.Order_ID || '').padStart(8, '0')}</p>
                        <p><strong>Ticket #:</strong> ${feedback.Order_TicketNumber || 'N/A'}</p>
                    </div>
                    <p><strong>Customer:</strong> ${feedback.Feedback_CustomerName}</p>
                    <p class="rating">
                        <strong>Rating:</strong> 
                        ${stars}
                        <span>(${feedback.Feedback_Rating}/5)</span>
                    </p>
                    <div class="feedback-comments">
                        <strong>Comments:</strong><br>
                        ${feedback.Feedback_Comments.replace(/\n/g, '<br>')}
                    </div>
                    <p class="feedback-datetime"><strong>Date/Time:</strong> ${formattedDate}</p>
                </div>
            `;
            
            feedbackList.append(feedbackItem);
        });
    }
    
    // Function to generate star rating HTML
    function generateStarRating(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star"></i>';
            } else {
                stars += '<i class="far fa-star"></i>';
            }
        }
        return stars;
    }
    
    // Function to update the chart
    function updateChart(ratingChart, ratings) {
        const ctx = document.getElementById('ratingChart').getContext('2d');
        
        // If chart already exists, destroy it
        if (window.ratingChartInstance) {
            window.ratingChartInstance.destroy();
        }
        
        // Create new chart
        window.ratingChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
                datasets: [{
                    label: 'Rating Distribution',
                    data: ratingChart,
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
    }
    
    // Function to update summary statistics
    function updateSummary(totalRatings, averageRating) {
        $('#total-ratings').text(totalRatings);
        $('#average-rating').text(averageRating);
    }
    
    // Function to update active filters display
    function updateActiveFilters(startDate, endDate, datePreset, ratings) {
        const activeFilters = $('.active-filters');
        activeFilters.empty();
        
        // Add date filter badge
        let dateLabel = '';
        switch(datePreset) {
            case 'today':
                dateLabel = 'Today';
                break;
            case 'yesterday':
                dateLabel = 'Yesterday';
                break;
            case 'last7days':
                dateLabel = 'Last 7 Days';
                break;
            case 'last30days':
                dateLabel = 'Last 30 Days';
                break;
            case 'thisMonth':
                dateLabel = 'This Month';
                break;
            case 'lastMonth':
                dateLabel = 'Last Month';
                break;
            case 'custom':
                dateLabel = `${startDate} to ${endDate}`;
                break;
            default:
                dateLabel = 'All Time';
        }
        
        if (dateLabel) {
            activeFilters.append(`
                <div class="filter-badge" data-filter="date">
                    <i class="fas fa-calendar-alt"></i> ${dateLabel}
                    <i class="fas fa-times" data-filter="date"></i>
                </div>
            `);
        }
        
        // Add rating filter badges
        if (ratings.length > 0) {
            ratings.forEach(function(rating) {
                activeFilters.append(`
                    <div class="filter-badge" data-filter="rating" data-rating="${rating}">
                        <i class="fas fa-star"></i> ${rating} Star${rating !== '1' ? 's' : ''}
                        <i class="fas fa-times" data-filter="rating" data-rating="${rating}"></i>
                    </div>
                `);
            });
        }
        
        // Handle filter badge removal
        $('.filter-badge .fa-times').on('click', function() {
            const filterType = $(this).parent().data('filter');
            
            if (filterType === 'date') {
                // Reset date filters to last 30 days
                const today = new Date();
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(today.getDate() - 29);
                
                $('#start-date').val(formatDate(thirtyDaysAgo));
                $('#end-date').val(formatDate(today));
                $('#date-preset').val('last30days');
            } else if (filterType === 'rating') {
                // Remove specific rating filter
                const rating = $(this).parent().data('rating');
                $(`.star-option[data-rating="${rating}"]`).removeClass('selected');
                
                // If no ratings left, set to "All"
                if ($('.star-option.selected').length === 0) {
                    $('#rating-filter').val('all');
                }
            }
            
            // Apply updated filters
            applyFilters();
        });
    }
    
    // Initial load with default filters
    applyFilters();
});
