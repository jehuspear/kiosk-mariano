// Define the handler function globally
window.cancelOrderHandler = async function(e) {
    if (e) e.preventDefault();
    
    try {
        const orderId = this.getAttribute('data-order-id');
        const orderRow = this.closest('.order');
        
        // Get order details from the row
        const orderDetails = {
            ticketNumber: orderRow.querySelector('.order-item:nth-child(1)').textContent,
            eatingOption: orderRow.querySelector('.order-item:nth-child(2)').textContent,
            items: orderRow.querySelector('.order-item:nth-child(4)').innerHTML,
            paymentMethod: orderRow.querySelector('.order-item:nth-child(5)').textContent,
            totalAmount: orderRow.querySelector('.order-item:nth-child(8)').textContent.replace('₱', '')
        };
        
        const confirmed = await adminModal.confirm({
            title: 'Cancel Order',
            message: 'Are you sure you want to cancel this order?',
            order: orderDetails
        });

        if (confirmed) {
            await cancelOrder(orderId);
        }
    } catch (error) {
        console.error('Error in cancelOrderHandler:', error);
    }
};

// Helper function to handle the actual cancellation
async function cancelOrder(orderId) {
    try {
        const formData = new FormData();
        formData.append('order_id', orderId);

        const response = await fetch('cancel_order.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if(data.success) {
            await adminModal.alert({
                title: 'Success',
                message: 'Order cancelled successfully!',
                type: 'success'
            });
            location.reload();
        } else {
            await adminModal.alert({
                title: 'Error',
                message: 'Error cancelling order: ' + (data.error || 'Unknown error'),
                type: 'error'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        await adminModal.alert({
            title: 'Error',
            message: 'Error cancelling order',
            type: 'error'
        });
    }
}
