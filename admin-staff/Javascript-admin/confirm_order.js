// Define the handler function globally
window.confirmOrderHandler = async function(e) {
    if (e) e.preventDefault();
    
    try {
        const orderId = this.getAttribute('data-order-id');
        const orderRow = this.closest('.order');
        
        if (!orderRow) {
            throw new Error('Could not find order row');
        }
        
        // Get order details from the row based on grid layout
        const orderDetails = {
            ticketNumber: orderRow.querySelector('.order-item:nth-child(1)').textContent.trim(),
            date: orderRow.querySelector('.order-item:nth-child(2)').textContent.trim(), // Now includes time
            eatingOption: orderRow.querySelector('.order-item:nth-child(3)').textContent.trim(),
            paymentMethod: orderRow.querySelector('.order-item:nth-child(4)').textContent.trim(),
            items: orderRow.querySelector('.order-item:nth-child(5)').innerHTML.trim(),
            itemPrices: orderRow.querySelector('.order-item:nth-child(6)').innerHTML.trim(),
            totalAmount: orderRow.querySelector('.order-item:nth-child(7)').textContent.trim().replace('₱', '')
        };

        // Debug log order details
        console.log('Order Details:', orderDetails);

        // Show modal and wait for user interaction
        const modalResult = await adminModal.confirm({
            title: 'Confirm Order',
            message: 'Is the order paid?',
            order: orderDetails
        });

        // Debug log modal result
        console.log('Modal Result:', modalResult);

        // Only proceed if user confirmed
        if (modalResult && modalResult.confirmed) {
            try {
                // Prepare payment data
                const paymentFormData = new URLSearchParams();
                
                // Basic data
                paymentFormData.append('order_id', orderId);
                paymentFormData.append('discount_type', modalResult.discountType || '');
                paymentFormData.append('discount_percent', modalResult.discountPercent || 0);
                paymentFormData.append('discount_amount', modalResult.discountAmount || 0);
                paymentFormData.append('final_amount', modalResult.finalAmount || parseFloat(orderDetails.totalAmount));

                // Payment method specific data
                if (orderDetails.paymentMethod === 'GCash') {
                    paymentFormData.append('reference_number', modalResult.referenceNumber || '');
                    paymentFormData.append('cash_paid', 0);
                    paymentFormData.append('change', 0);
                } else {
                    paymentFormData.append('reference_number', '');
                    paymentFormData.append('cash_paid', modalResult.cashAmount || 0);
                    paymentFormData.append('change', modalResult.change || 0);
                }

                // Debug log payment data
                console.log('Sending payment update:', Object.fromEntries(paymentFormData));

                const response = await fetch('confirm_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: paymentFormData.toString()
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server Error Response:', errorText);
                    throw new Error(`Server error: ${response.status}`);
                }

                const serverResult = await response.json();
                console.log('Server Response:', serverResult);

                if (!serverResult.success) {
                    throw new Error(serverResult.error || 'Failed to process order');
                }

                // Show success message and reload
                await adminModal.alert({
                    title: 'Success',
                    message: serverResult.message || 'Order confirmed and now preparing!',
                    type: 'success'
                });
                location.reload();

            } catch (error) {
                console.error('Error processing order:', error);
                await adminModal.alert({
                    title: 'Error',
                    message: error.message,
                    type: 'error'
                });
            }
        }
    } catch (error) {
        console.error('Error in confirmOrderHandler:', error);
        await adminModal.alert({
            title: 'Error',
            message: error.message || 'Error processing order',
            type: 'error'
        });
    }
};
