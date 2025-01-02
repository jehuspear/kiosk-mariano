document.addEventListener('DOMContentLoaded', function() {
    fetchCompletedOrders();
    
    // Refresh every 30 seconds
    setInterval(fetchCompletedOrders, 30000);
});

function fetchCompletedOrders() {
    console.log('Fetching completed orders...');
    fetch('get_completed_orders.php')
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if(data.success) {
                updateOrdersTable(data.orders);
                updateSalesSummary(data);
            } else {
                console.error('Error fetching orders:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('.orders-container').innerHTML = 
                '<div class="error-message">Error loading orders. Please check the console for details.</div>';
        });
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2);
}

function updateOrdersTable(orders) {
    const tableBody = document.querySelector('.orders-container');
    tableBody.innerHTML = '';
    
    orders.forEach(order => {
        const row = document.createElement('div');
        row.className = 'order-row';
        
        row.innerHTML = `
            <div class="order-item">${order.Order_ID}</div>
            <div class="order-item">${order.Order_TicketNumber}</div>
            <div class="order-item">${order.Order_EatingOption}</div>
            <div class="order-item">${order.MenuItemIDs}</div>
            <div class="order-item">${order.OrderItems}</div>
            <div class="order-item">${order.Payment_Method}</div>
            <div class="order-item">${order.TimeDifference}</div>
            <div class="order-item">₱${order.Cost}</div>
            <div class="order-item">${order.Discount}%</div>
            <div class="order-item">₱${order.Total}</div>
        `;
        
        tableBody.appendChild(row);
    });
}

function updateSalesSummary(data) {
    document.querySelector('.total-orders').textContent = data.totalOrders;
    document.querySelector('.cash-sales').textContent = formatCurrency(data.totalCashSales);
    document.querySelector('.gcash-sales').textContent = formatCurrency(data.totalGcashSales);
    document.querySelector('.total-sales').textContent = formatCurrency(data.totalSales);
}
