document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    let currentAction = null;
    let currentOrderId = null;

    // Handle action buttons (approve/decline)
    function setupActionButtons() {
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentOrderId = this.getAttribute('data-order-id');
                currentAction = this.classList.contains('approve') ? 'approve' : 'decline';
                
                // Set confirmation text and button styles based on action
                const confirmText = currentAction === 'approve' 
                    ? 'Mark this order as ready to claim?' 
                    : 'Are you sure you want to cancel this order?';
                
                document.getElementById('confirmationText').textContent = confirmText;
                
                const confirmBtn = document.getElementById('confirmActionBtn');
                if (currentAction === 'approve') {
                    confirmBtn.classList.remove('btn-danger');
                    confirmBtn.classList.add('btn-success');
                    confirmBtn.textContent = 'Mark as Ready';
                } else {
                    confirmBtn.classList.remove('btn-success');
                    confirmBtn.classList.add('btn-danger');
                    confirmBtn.textContent = 'Cancel Order';
                }
                
                // Show confirmation modal
                confirmationModal.show();
            });
        });
    }

    // Handle confirmation button click
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        if (!currentOrderId || !currentAction) return;
        
        const formData = new FormData();
        formData.append('order_id', currentOrderId);
        formData.append('action', currentAction);
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        // Send request to update order status
        fetch('update_preparing_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const orderCard = document.querySelector(`[data-order-id="${currentOrderId}"]`).closest('.order-card');
                orderCard.style.opacity = '0';
                setTimeout(() => {
                    orderCard.remove();
                    
                    // Check if there are no more orders
                    if (document.querySelectorAll('.order-card').length === 0) {
                        const orderList = document.querySelector('.order-list');
                        orderList.innerHTML = '<p class="no-orders">No orders in preparation at the moment.</p>';
                    }
                }, 300);
                
                confirmationModal.hide();
            } else {
                throw new Error(data.error || 'Failed to update order status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            this.disabled = false;
            this.textContent = currentAction === 'approve' ? 'Mark as Ready' : 'Cancel Order';
        });
    });

    // Setup initial event listeners
    setupActionButtons();

    // Auto-refresh orders every 30 seconds
    setInterval(() => {
        fetch('get_preparing_orders.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const orderList = document.querySelector('.order-list');
                    if (data.orders.length > 0) {
                        orderList.innerHTML = data.orders.map(order => `
                            <div class="order-card" style="opacity: 0;">
                                <div class="order-header">
                                    <div class="ticket-info">
                                        <h3>Ticket #${order.ticket_number}</h3>
                                        <span class="status-badge preparing">
                                            ${order.status}
                                        </span>
                                    </div>
                                    <div class="order-time">
                                        ${order.datetime}
                                    </div>
                                </div>
                                
                                <div class="order-details">
                                    <div class="order-type">
                                        <i class="fas ${order.eating_option === 'Dine-in' ? 'fa-utensils' : 'fa-shopping-bag'}"></i>
                                        ${order.eating_option}
                                    </div>
                                    <div class="payment-method">
                                        <i class="fas ${order.payment_method === 'Cash' ? 'fa-money-bill' : 'fa-mobile-alt'}"></i>
                                        ${order.payment_method}
                                    </div>
                                </div>
                                
                                <div class="order-items">
                                    <h4>Items:</h4>
                                    <div class="items-list">
                                        ${order.items}
                                    </div>
                                </div>
                                
                                <div class="order-footer">
                                    <div class="total-amount">
                                        <strong>Total:</strong> ₱${order.total_amount}
                                    </div>
                                    <div class="action-buttons">
                                        <button class="action-btn decline" data-order-id="${order.order_id}" title="Cancel Order">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="action-btn approve" data-order-id="${order.order_id}" title="Mark as Ready">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        
                        // Fade in new cards
                        setTimeout(() => {
                            document.querySelectorAll('.order-card').forEach(card => {
                                card.style.opacity = '1';
                            });
                        }, 100);
                        
                        // Reattach event listeners
                        setupActionButtons();
                    } else {
                        orderList.innerHTML = '<p class="no-orders">No orders in preparation at the moment.</p>';
                    }
                }
            })
            .catch(console.error);
    }, 30000);
});
