document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTicket');
    const orders = document.querySelectorAll('.order');
    const orderContainer = document.querySelector('.order-details-container');

    // Create no results message element
    const noResults = document.createElement('div');
    noResults.className = 'no-results';
    noResults.innerHTML = '<p>No orders found matching this ticket number.</p>';
    noResults.style.display = 'none';
    orderContainer.appendChild(noResults);

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let hasVisibleOrders = false;

        orders.forEach(order => {
            const ticketNumber = order.querySelector('.order-item:first-child').textContent.toLowerCase();
            
            if (ticketNumber.includes(searchTerm)) {
                order.style.display = '';
                hasVisibleOrders = true;
            } else {
                order.style.display = 'none';
            }
        });

        // Show/hide no results message
        noResults.style.display = hasVisibleOrders ? 'none' : 'block';
    });
});
