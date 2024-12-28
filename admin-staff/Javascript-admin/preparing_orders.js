document.addEventListener('DOMContentLoaded', function() {
    const orderList = document.querySelector('.order-list');
    
    // Function to load preparing orders
    function loadPreparingOrders() {
        fetch('get_preparing_orders.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.orders.length > 0) {
                        orderList.innerHTML = data.orders.map(order => `
                            <div class="order-card">
                                <div class="order-info">
                                    <p class="ticket-number">Order Ticket No</p>
                                    <h3>${order.ticket_number}</h3>
                                    <p>Order Time: ${order.payment_datetime}</p>
                                    <p>Payment Method: ${order.payment_method}</p>
                                    <p>Payment Total: ₱${order.total_amount}</p>
                                    <button class="view-order" data-order-id="${order.order_id}" 
                                            data-items="${order.items}"
                                            data-eating-option="${order.eating_option}"
                                            data-ticket-no="${order.ticket_number}"
                                            data-total="${order.total_amount}">
                                        View order list
                                    </button>
                                </div>
                                <div class="order-actions">
                                    <button class="action-btn decline" data-order-id="${order.order_id}">✖</button>
                                    <button class="action-btn approve" data-order-id="${order.order_id}">✔</button>
                                </div>
                            </div>
                        `).join('');
                        
                        // Reattach event listeners
                        attachEventListeners();
                    } else {
                        orderList.innerHTML = '<p class="no-orders">No orders in preparation at the moment.</p>';
                    }
                } else {
                    console.error('Error loading orders');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                orderList.innerHTML = '<p class="no-orders">Error loading orders. Please try again.</p>';
            });
    }
    
    // Function to attach event listeners to dynamically created elements
    function attachEventListeners() {
        // View order button clicks
        document.querySelectorAll('.view-order').forEach(button => {
            button.addEventListener('click', function() {
                const items = this.getAttribute('data-items');
                const eatingOption = this.getAttribute('data-eating-option');
                const ticketNo = this.getAttribute('data-ticket-no');
                const total = this.getAttribute('data-total');
                
                // Update modal content
                document.querySelector('#orderDetails').innerHTML = items;
                document.querySelector('.modal-footer p:nth-child(1)').innerHTML = `<strong>Ticket No:</strong> ${ticketNo}`;
                document.querySelector('.modal-footer p:nth-child(3)').innerHTML = `<strong>Total:</strong> ₱${total}`;
                
                // Add eating option to each order row
                const orderRows = document.querySelectorAll('.order-row p');
                orderRows.forEach(row => {
                    row.innerHTML = row.innerHTML.replace(/(<br>.*?<br>)/, `<br>${eatingOption}<br>`);
                });
                
                const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                orderModal.show();
            });
        });

        // Action buttons (approve/decline)
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                const isApprove = this.classList.contains('approve');
                
                // Set confirmation text
                document.getElementById('confirmationText').textContent = 
                    isApprove ? 'Mark this order as ready to claim?' : 'Cancel this order?';
                
                // Store action type and order ID
                document.getElementById('confirmActionBtn').setAttribute('data-action', isApprove ? 'approve' : 'decline');
                document.getElementById('confirmActionBtn').setAttribute('data-order-id', orderId);
                
                const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                confirmationModal.show();
            });
        });
    }
    
    // Initial load
    loadPreparingOrders();
    
    // Refresh orders every 30 seconds
    setInterval(loadPreparingOrders, 30000);
    
    // Handle confirmation button click
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        const orderId = this.getAttribute('data-order-id');
        
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('action', action);
        
        fetch('update_order_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const confirmationModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                confirmationModal.hide();
                loadPreparingOrders(); // Refresh the orders list
            } else {
                alert('Error updating order status: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    });
});
