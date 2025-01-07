document.addEventListener('DOMContentLoaded', function() {
    // Handle Confirm button clicks
    document.querySelectorAll('.done-button').forEach(button => {
        button.addEventListener('click', async function() {
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
                title: 'Confirm Order',
                message: 'Is the order paid?',
                order: orderDetails
            });

            if (confirmed) {
                confirmOrder(orderId);
            }
        });
    });

    function confirmOrder(orderId) {
        const formData = new FormData();
        formData.append('order_id', orderId);

        fetch('confirm_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(async data => {
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
        })
        .catch(async error => {
            console.error('Error:', error);
            await adminModal.alert({
                title: 'Error',
                message: 'Error confirming order',
                type: 'error'
            });
        });
    }
});
