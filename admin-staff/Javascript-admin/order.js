document.addEventListener('DOMContentLoaded', function() {
    // Handle Confirm button clicks
    document.querySelectorAll('.done-button').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            
            // Store the order ID for use in the confirmation
            document.getElementById('yesButton').setAttribute('data-order-id', orderId);
            modal.show();
        });
    });

    // Handle Yes button click in confirmation modal
    document.getElementById('yesButton').addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        updateOrderStatus(orderId, 'Preparing');
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
        modal.hide();
    });

    // Handle Cancel button clicks
    document.querySelectorAll('.button-cancel').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            if(confirm('Are you sure you want to cancel this order?')) {
                updateOrderStatus(orderId, 'Cancelled');
            }
        });
    });

    function updateOrderStatus(orderId, status) {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', status);

        fetch('update_order_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Show success message
                alert(status === 'Preparing' ? 'Order confirmed and now preparing!' : 'Order cancelled successfully!');
                // Refresh the page to show updated orders
                location.reload();
            } else {
                alert('Error updating order status: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    }
});
