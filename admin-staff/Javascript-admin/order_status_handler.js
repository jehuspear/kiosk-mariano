// Make handler functions globally available
window.handleOrderAction = async function(orderId, action) {
    try {
        // Convert action to match backend expectations
        const backendAction = action === 'ready' ? 'approve' : 'decline';
        
        const confirmMessage = action === 'ready' ? 
            'Are you sure you want to mark this order as ready?' : 
            'Are you sure you want to cancel this order?';

        // Show confirmation modal
        const confirmText = document.getElementById('confirmationText');
        const confirmBtn = document.getElementById('confirmActionBtn');
        
        confirmText.textContent = confirmMessage;
        window.confirmationModal.show();

        // Handle confirmation
        return new Promise((resolve) => {
            const handleConfirm = async () => {
                // Remove event listener to prevent memory leaks
                confirmBtn.removeEventListener('click', handleConfirm);
                
                try {
                    const formData = new FormData();
                    formData.append('order_id', orderId);
                    formData.append('action', backendAction);

                    const response = await fetch('handle_order_status.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    window.confirmationModal.hide();

                    if (result.success) {
                        // Show success message
                        const successMessage = action === 'ready' ? 
                            'Order marked as ready!' : 
                            'Order cancelled successfully!';
                            
                        if (window.adminModal) {
                            await adminModal.alert({
                                title: 'Success',
                                message: successMessage,
                                type: 'success'
                            });
                        }
                        
                        // Refresh the page
                        location.reload();
                    } else {
                        throw new Error(result.error || 'Unknown error occurred');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    window.confirmationModal.hide();
                    
                    if (window.adminModal) {
                        await adminModal.alert({
                            title: 'Error',
                            message: 'Failed to update order status: ' + error.message,
                            type: 'error'
                        });
                    }
                }
                
                resolve();
            };

            // Add event listener for confirmation
            confirmBtn.addEventListener('click', handleConfirm);

            // Handle modal close/cancel
            const handleClose = () => {
                confirmBtn.removeEventListener('click', handleConfirm);
                window.confirmationModal.hide();
                resolve();
            };

            // Add event listeners for close buttons
            document.querySelector('#confirmationModal .btn-close').addEventListener('click', handleClose);
            document.querySelector('#confirmationModal .btn-secondary').addEventListener('click', handleClose);
        });
    } catch (error) {
        console.error('Error in handleOrderAction:', error);
        if (window.adminModal) {
            await adminModal.alert({
                title: 'Error',
                message: 'An unexpected error occurred',
                type: 'error'
            });
        }
    }
};

// Initialize event listeners
function initializeOrderHandlers() {
    // Handle approve buttons
    document.querySelectorAll('.action-btn.approve').forEach(button => {
        const orderId = button.getAttribute('data-order-id');
        button.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            handleOrderAction(orderId, 'ready');
        };
    });

    // Handle decline buttons
    document.querySelectorAll('.action-btn.decline').forEach(button => {
        const orderId = button.getAttribute('data-order-id');
        button.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            handleOrderAction(orderId, 'cancel');
        };
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initializeOrderHandlers);

// Export for auto-refresh
window.initializeOrderHandlers = initializeOrderHandlers;
