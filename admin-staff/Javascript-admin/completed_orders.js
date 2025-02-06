$(document).ready(function() {
    // Initialize datepicker with enhanced options
    $('.input-group.date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        clearBtn: true,
        orientation: "bottom auto",
        templates: {
            leftArrow: '<i class="fas fa-chevron-left"></i>',
            rightArrow: '<i class="fas fa-chevron-right"></i>'
        },
        showWeekDays: true,
        maxViewMode: 2,
        beforeShowDay: function(date) {
            var day = date.getDay();
            if (day === 0 || day === 6) {
                return { classes: 'weekend' };
            }
            return {};
        }
    });

    // Set default date to today
    $('.input-group.date').datepicker('setDate', new Date());

    // Add change event listener to datepicker
    $('.input-group.date').on('changeDate', function() {
        fetchOrders();
    });

    // Add clear button functionality
    $('.input-group.date').on('clearDate', function() {
        $(this).datepicker('setDate', new Date());
        fetchOrders();
    });

    let allOrders = []; // Store all orders for filtering
    let searchTimeout; // For debouncing search

    // Search functionality with debouncing
    $('#ticketSearch').on('input', function() {
        const searchInput = $(this);
        const searchIcon = $('#clearSearch i');
        const searchValue = searchInput.val().trim();
        
        // Update search icon based on input
        if (searchValue.length > 0) {
            searchIcon.removeClass('fa-search').addClass('fa-times');
        } else {
            searchIcon.removeClass('fa-times').addClass('fa-search');
        }

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Set new timeout for search
        searchTimeout = setTimeout(() => {
            filterOrders(searchValue);
        }, 300); // 300ms delay
    });

    // Clear search button functionality
    $('#clearSearch').on('click', function() {
        const searchInput = $('#ticketSearch');
        const searchIcon = $(this).find('i');
        
        if (searchInput.val().length > 0) {
            // Clear search
            searchInput.val('');
            searchIcon.removeClass('fa-times').addClass('fa-search');
            filterOrders('');
        }
    });

    // Initial fetch
    fetchOrders();
});

function padTicketNumber(number) {
    return number.toString().padStart(3, '0');
}

function filterOrders(searchValue) {
    searchValue = searchValue.toLowerCase();
    const filteredOrders = allOrders.filter(order => {
        const ticketNumber = padTicketNumber(order.Order_TicketNumber);
        return ticketNumber.includes(searchValue);
    });

    displayOrders(filteredOrders, searchValue);
    updateSalesSummary({
        totalOrders: filteredOrders.length,
        totalCashSales: calculateTotalSales(filteredOrders, 'Cash'),
        totalGcashSales: calculateTotalSales(filteredOrders, 'GCash'),
        totalSales: calculateTotalSales(filteredOrders)
    });
}

function calculateTotalSales(orders, paymentMethod = null) {
    return orders.reduce((total, order) => {
        if (!paymentMethod || order.Payment_Method === paymentMethod) {
            return total + parseFloat(order.Total.replace('₱', '').replace(',', ''));
        }
        return total;
    }, 0);
}

function fetchOrders() {
    const selectedDate = $('#datepicker').val();
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'text-center my-3';
    loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading orders...';
    
    const container = document.querySelector('.orders-container');
    container.innerHTML = '';
    container.appendChild(loadingDiv);

    fetch(`get_completed_orders.php?date=${selectedDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allOrders = data.orders;
                const searchValue = $('#ticketSearch').val().trim();
                if (searchValue) {
                    filterOrders(searchValue);
                } else {
                    displayOrders(data.orders);
                    updateSalesSummary(data);
                }
                updateDateDisplay(selectedDate);
            } else {
                container.innerHTML = `<div class="alert alert-danger" role="alert">
                    Error loading orders: ${data.message}
                </div>`;
                console.error('Error fetching orders:', data.message);
            }
        })
        .catch(error => {
            container.innerHTML = `<div class="alert alert-danger" role="alert">
                Failed to load orders. Please try again.
            </div>`;
            console.error('Error:', error);
        });
}

function updateDateDisplay(selectedDate) {
    const date = new Date(selectedDate);
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const formattedDate = date.toLocaleDateString('en-US', options);
    
    // Update the header text
    $('.text-wrapper-10').html(`Completed Orders<br><small style="font-size: 0.8em; color: #666;">${formattedDate}</small>`);
    
    // Update the formatted date in summary
    $('.formatted-date').text(formattedDate);
}

function displayOrders(orders, searchValue = '') {
    const container = document.querySelector('.orders-container');
    container.innerHTML = '';

    if (orders.length === 0) {
        container.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search"></i>
                <p>${searchValue ? 'No orders found matching ticket number: ' + searchValue : 'No orders found for this date'}</p>
            </div>`;
        return;
    }

    orders.forEach(order => {
        const orderDiv = document.createElement('div');
        orderDiv.className = 'order-row';
        
        // Highlight matching ticket number if search value exists
        const ticketNumber = padTicketNumber(order.Order_TicketNumber);
        const highlightedTicket = searchValue ? 
            ticketNumber.replace(new RegExp(searchValue, 'gi'), match => `<span class="highlight">${match}</span>`) :
            ticketNumber;

        orderDiv.innerHTML = `
            <div class="order-item">${order.Order_ID}</div>
            <div class="order-item">${highlightedTicket}</div>
            <div class="order-item">${order.Order_EatingOption}</div>
            <div class="order-item">${order.MenuItemIDs}</div>
            <div class="order-item">${order.OrderItems}</div>
            <div class="order-item">${order.Payment_Method}</div>
            <div class="order-item">${order.TimeDifference}</div>
            <div class="order-item">₱${order.Cost}</div>
            <div class="order-item">${order.Discount}%</div>
            <div class="order-item">₱${order.Total}</div>
        `;
        container.appendChild(orderDiv);
    });
}

function updateSalesSummary(data) {
    document.querySelector('.total-orders').textContent = data.totalOrders;
    document.querySelector('.cash-sales').textContent = `₱${data.totalCashSales.toFixed(2)}`;
    document.querySelector('.gcash-sales').textContent = `₱${data.totalGcashSales.toFixed(2)}`;
    document.querySelector('.total-sales').textContent = `₱${data.totalSales.toFixed(2)}`;
}
