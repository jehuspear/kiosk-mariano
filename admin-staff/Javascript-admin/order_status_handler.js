document.addEventListener('DOMContentLoaded', function() {
    // Function to show notification
    function showNotification(message, isError = false) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${isError ? 'danger' : 'success'} position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; opacity: 0; transition: opacity 0.3s ease;';
        notification.textContent = message;
        document.body.appendChild(notification);

        // Trigger reflow for animation
        notification.offsetHeight;
        notification.style.opacity = '1';

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Handle action buttons (approve/decline)
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const isApprove = this.classList.contains('approve');
            
            // Set confirmation text and button style
            document.getElementById('confirmationText').textContent = 
                isApprove ? 'Mark this order as ready to claim?' : 'Are you sure you want to cancel this order?';
            
            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.className = `btn ${isApprove ? 'btn-success' : 'btn-danger'}`;
            confirmBtn.textContent = isApprove ? 'Mark as Ready' : 'Cancel Order';
            
            // Store action data
            confirmBtn.setAttribute('data-order-id', orderId);
            confirmBtn.setAttribute('data-action', isApprove ? 'approve' : 'decline');
            
            // Show confirmation modal
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmationModal.show();
        });
    });

    // Handle confirmation button click
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        const action = this.getAttribute('data-action');
        
        if (!orderId || !action) return;
        
        // Show loading state
        this.disabled = true;
        const originalText = this.textContent;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        
        // Send request to update order status
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('action', action);
        
        fetch('handle_order_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the order card with animation
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`).closest('.order-card');
                orderCard.style.opacity = '0';
                orderCard.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    orderCard.remove();
                    
                    // Show no orders message if no orders left
                    if (document.querySelectorAll('.order-card').length === 0) {
                        const orderList = document.querySelector('.order-list');
                        orderList.innerHTML = '<p class="no-orders">No orders in preparation at the moment.</p>';
                    }
                }, 300);
                
                // Hide modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                modal.hide();
                
                // Show success notification
                showNotification(data.message);
            } else {
                throw new Error(data.error || 'Failed to update order status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message, true);
        })
        .finally(() => {
            // Reset button state
            this.disabled = false;
            this.textContent = originalText;
        });
    });
});
