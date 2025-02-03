// Define the handler function globally
window.confirmOrderHandler = async function(e) {
    if (e) e.preventDefault();
    
    try {
        const orderId = this.getAttribute('data-order-id');
        const orderRow = this.closest('.order');
        
        // Get order details from the row
        const orderDetails = {
            ticketNumber: orderRow.querySelector('.order-item:nth-child(1)').textContent,
            eatingOption: orderRow.querySelector('.order-item:nth-child(3)').textContent,
            items: orderRow.querySelector('.order-item:nth-child(5)').innerHTML,
            paymentMethod: orderRow.querySelector('.order-item:nth-child(4)').textContent,
            totalAmount: orderRow.querySelector('.order-item:nth-child(7)').textContent.replace('₱', '')
        };
        
        const confirmed = await adminModal.confirm({
            title: 'Confirm Order',
            message: 'Is the order paid?',
            order: orderDetails
        });

        if (confirmed) {
            await confirmOrder(orderId);
        }
    } catch (error) {
        console.error('Error in confirmOrderHandler:', error);
    }
};

// Helper function to handle the actual confirmation
async function confirmOrder(orderId) {
    try {
        const formData = new FormData();
        formData.append('order_id', orderId);

        const response = await fetch('confirm_order.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if(data.success) {
            await adminModal.alert({
                title: 'Success',
                message: 'Order confirmed and now preparing!',
                type: 'success'
            });
            location.reload();
        } else {
            await adminModal.alert({
                title: 'Error',
                message: 'Error confirming order: ' + (data.error || 'Unknown error'),
                type: 'error'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        await adminModal.alert({
            title: 'Error',
            message: 'Error confirming order',
            type: 'error'
        });
    }
}
