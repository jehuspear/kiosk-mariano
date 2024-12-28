document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    
    // Handle view order button clicks
    document.querySelectorAll('.view-order').forEach(button => {
        button.addEventListener('click', function() {
            // Get order details from data attributes
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
            
            orderModal.show();
        });
    });

    // Handle action buttons (approve/decline)
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const isApprove = this.classList.contains('approve');
            
            // Set confirmation text based on action
            document.getElementById('confirmationText').textContent = 
                isApprove ? 'Mark this order as ready to claim?' : 'Cancel this order?';
            
            // Store action type and order ID for confirmation
            document.getElementById('confirmActionBtn').setAttribute('data-action', isApprove ? 'approve' : 'decline');
            document.getElementById('confirmActionBtn').setAttribute('data-order-id', orderId);
            
            confirmationModal.show();
        });
    });

    // Handle confirmation button click
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        const orderId = this.getAttribute('data-order-id');
        
        // Create form data
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('action', action);
        
        // Send request to update order status
        fetch('update_order_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide modal and refresh page
                confirmationModal.hide();
                location.reload();
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
