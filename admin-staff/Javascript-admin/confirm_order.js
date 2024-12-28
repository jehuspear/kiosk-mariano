document.addEventListener('DOMContentLoaded', function() {
    // Get the confirmation modal
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    let currentOrderId = null;

    // Handle Confirm button clicks
    document.querySelectorAll('.done-button').forEach(button => {
        button.addEventListener('click', function() {
            currentOrderId = this.getAttribute('data-order-id');
            confirmModal.show();
        });
    });

    // Handle Yes button click in confirmation modal
    document.getElementById('yesButton').addEventListener('click', function() {
        if (currentOrderId) {
            confirmOrder(currentOrderId);
            confirmModal.hide();
        }
    });

    function confirmOrder(orderId) {
        const formData = new FormData();
        formData.append('order_id', orderId);

        fetch('confirm_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Order confirmed and now preparing!');
                // Refresh the page to show updated orders
                location.reload();
            } else {
                alert('Error confirming order: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error confirming order');
        });
    }
});
