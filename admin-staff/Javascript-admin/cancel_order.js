// Define the handler function globally
window.cancelOrderHandler = async function(e) {
    if (e) e.preventDefault();
    
    try {
        const orderId = this.getAttribute('data-order-id');
        const orderRow = this.closest('.order');
        
        // Get order details from the row
        const orderDetails = {
            ticketNumber: orderRow.querySelector('.order-item:nth-child(1)').textContent,
            date: orderRow.querySelector('.order-item:nth-child(2)').textContent.trim(), // Now includes time
            eatingOption: orderRow.querySelector('.order-item:nth-child(3)').textContent,
            items: orderRow.querySelector('.order-item:nth-child(5)').innerHTML,
            itemPrices: orderRow.querySelector('.order-item:nth-child(6)').innerHTML,
            paymentMethod: orderRow.querySelector('.order-item:nth-child(4)').textContent,
            totalAmount: orderRow.querySelector('.order-item:nth-child(7)').textContent.replace('₱', '')
        };
        
        const modalResult = await adminModal.confirm({
            title: 'Cancel Order',
            message: 'Are you sure you want to cancel this order?',
            order: orderDetails
        });

        // Only proceed if user clicked Yes
        if (modalResult && modalResult.confirmed) {
            await cancelOrder(orderId);
        }
        // If user clicked No, do nothing and modal will close
    } catch (error) {
        console.error('Error in cancelOrderHandler:', error);
        await adminModal.alert({
            title: 'Error',
            message: error.message || 'Error processing cancellation',
            type: 'error'
        });
    }
};

// Helper function to handle the actual cancellation
async function cancelOrder(orderId) {
    try {
        const formData = new URLSearchParams();
        formData.append('order_id', orderId);

        const response = await fetch('cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: formData.toString()
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server Error Response:', errorText);
            throw new Error(`Server error: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            await adminModal.alert({
                title: 'Success',
                message: data.message || 'Order cancelled successfully!',
                type: 'success'
            });
            location.reload();
        } else {
            throw new Error(data.error || 'Failed to cancel order');
        }
    } catch (error) {
        console.error('Error:', error);
        await adminModal.alert({
            title: 'Error',
            message: error.message || 'Error cancelling order',
            type: 'error'
        });
        throw error; // Re-throw to be handled by caller
    }
}
