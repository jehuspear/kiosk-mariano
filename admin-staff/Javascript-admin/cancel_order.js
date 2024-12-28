document.addEventListener('DOMContentLoaded', function() {
    // Get the cancel confirmation modal
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelConfirmationModal'));
    let currentOrderId = null;

    // Handle Cancel button clicks
    document.querySelectorAll('.button-cancel').forEach(button => {
        button.addEventListener('click', function() {
            currentOrderId = this.getAttribute('data-order-id');
            cancelModal.show();
        });
    });

    // Handle confirm cancel button click
    document.getElementById('confirmCancelButton').addEventListener('click', function() {
        if (currentOrderId) {
            cancelOrder(currentOrderId);
            cancelModal.hide();
        }
    });

    function cancelOrder(orderId) {
        const formData = new FormData();
        formData.append('order_id', orderId);

        fetch('cancel_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Order cancelled successfully!');
                // Refresh the page to show updated orders
                location.reload();
            } else {
                alert('Error cancelling order: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
});
