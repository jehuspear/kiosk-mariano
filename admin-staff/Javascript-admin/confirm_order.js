// Define the handler function globally
window.confirmOrderHandler = async function(e) {
    if (e) e.preventDefault();
    
    try {
        const orderId = this.getAttribute('data-order-id');
        const orderRow = this.closest('.order');
        
        // Debug logging for order identification
        console.group('Order Identification');
        console.log('Order ID:', orderId);
        console.log('Order Row Found:', !!orderRow);
        if (orderRow) {
            console.log('Ticket Number:', orderRow.querySelector('.order-item:nth-child(1)').textContent);
            console.log('Order Date:', orderRow.querySelector('.order-item:nth-child(2)').textContent);
        }
        console.groupEnd();

        if (!orderRow) {
            throw new Error('Could not find order row');
        }
        
        // Get order details from the row
        const orderDetails = {
            ticketNumber: orderRow.querySelector('.order-item:nth-child(1)').textContent,
            eatingOption: orderRow.querySelector('.order-item:nth-child(3)').textContent,
            items: orderRow.querySelector('.order-item:nth-child(5)').innerHTML,
            itemPrices: orderRow.querySelector('.order-item:nth-child(6)').innerHTML,
            paymentMethod: orderRow.querySelector('.order-item:nth-child(4)').textContent,
            totalAmount: orderRow.querySelector('.order-item:nth-child(7)').textContent.replace('₱', '')
        };

        // Debug logging for order details
        console.group('Order Details');
        console.log('Order Details:', orderDetails);
        console.log('Raw Total Amount:', orderRow.querySelector('.order-item:nth-child(7)').textContent);
        console.log('Processed Total Amount:', orderDetails.totalAmount);
        console.groupEnd();
        
        // Show modal and wait for user interaction
        const result = await adminModal.confirm({
            title: 'Confirm Order',
            message: 'Is the order paid?',
            order: orderDetails
        });

        // Only proceed if user confirmed and provided payment details
        if (result && result.confirmed) {
            try {
                // Debug logging
                console.log('Modal result:', result);
                console.log('Order details:', orderDetails);

                // Validate payment method
                const paymentMethod = orderDetails.paymentMethod.trim();
                if (!['GCash', 'Cash'].includes(paymentMethod)) {
                    throw new Error('Invalid payment method');
                }

                // Get final amount
                const finalAmount = parseFloat(result.finalAmount) || parseFloat(orderDetails.totalAmount);
                if (isNaN(finalAmount) || finalAmount < 0) {
                    throw new Error('Invalid final amount');
                }

                // Prepare payment data
                const paymentData = {
                    discount_type: result.discountType || '',
                    discount_percent: parseFloat(result.discountPercent) || 0,
                    discount_amount: parseFloat(result.discountAmount) || 0,
                    final_amount: finalAmount,
                    reference_number: '',
                    cash_paid: 0,
                    change: 0
                };

                // Validate and add payment method specific details
                if (paymentMethod === 'GCash') {
                    const referenceNumber = result.referenceNumber?.trim();
                    if (!referenceNumber || referenceNumber.length !== 6) {
                        throw new Error('Please enter a valid 6-digit GCash reference number');
                    }
                    paymentData.reference_number = referenceNumber;
                } else if (paymentMethod === 'Cash') {
                    const cashAmount = parseFloat(result.cashAmount);
                    if (isNaN(cashAmount) || cashAmount <= 0) {
                        throw new Error('Please enter a valid cash amount');
                    }
                    if (cashAmount < finalAmount) {
                        throw new Error(`Cash amount (₱${cashAmount}) must be greater than or equal to total amount (₱${finalAmount})`);
                    }
                    paymentData.cash_paid = cashAmount;
                    paymentData.change = cashAmount - finalAmount;
                }

                // Debug logging
                console.log('Prepared payment data:', paymentData);

                // Debug logging before server request
                console.group('Server Request Data');
                console.log('Order ID to be sent:', orderId);
                console.log('Payment Data to be sent:', paymentData);
                console.groupEnd();

                // Send to server
                await confirmOrder(orderId, paymentData);
            } catch (error) {
                console.error('Error preparing payment data:', error);
                await adminModal.alert({
                    title: 'Error',
                    message: error.message,
                    type: 'error'
                });
                return;
            }
        }
    } catch (error) {
        console.error('Error in confirmOrderHandler:', error);
        const errorMessage = error.message || 'Error processing order';
        await adminModal.alert({
            title: 'Error',
            message: errorMessage,
            type: 'error'
        });
    }
};

// Helper function to handle the actual confirmation
async function confirmOrder(orderId, paymentData) {
    try {
        // Debug logging
        console.log('Sending payment data:', paymentData);

        // Validate data before sending
        if (!orderId) throw new Error('Order ID is required');
        if (typeof paymentData.final_amount !== 'number' || isNaN(paymentData.final_amount)) {
            throw new Error('Invalid final amount');
        }

        // Validate all required fields are present
        if (!orderId || !paymentData || typeof paymentData !== 'object') {
            throw new Error('Missing required data');
        }

        // Validate all numeric fields
        const numericFields = {
            final_amount: paymentData.final_amount,
            discount_percent: paymentData.discount_percent,
            discount_amount: paymentData.discount_amount,
            cash_paid: paymentData.cash_paid,
            change: paymentData.change
        };

        for (const [field, value] of Object.entries(numericFields)) {
            if (typeof value !== 'number' || isNaN(value)) {
                throw new Error(`Invalid ${field.replace('_', ' ')}`);
            }
        }

        // Create FormData object
        const formData = new FormData();
        Object.entries(paymentData).forEach(([key, value]) => {
            formData.append(key, String(value));
        });
        formData.append('order_id', orderId);

        // Debug logging
        for (let [key, value] of formData.entries()) {
            console.log(`Sending ${key}:`, value);
        }

        // Add timestamp to prevent caching
        const timestamp = new Date().getTime();
        const url = `confirm_order.php?_=${timestamp}`;

        let response, text;
        
        // Add request timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

        try {
            // Make the request
            response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin',
                signal: controller.signal
            });

            // Debug logging
            console.group('Server Response');
            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));
            console.groupEnd();

            // Check if response is ok
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server error response:', errorText);
                
                // Try to parse error response as JSON
                try {
                    const errorJson = JSON.parse(errorText);
                    throw new Error(errorJson.error || `Server error: ${response.status} ${response.statusText}`);
                } catch (e) {
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                }
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timed out. Please try again.');
            }
            throw error;
        } finally {
            clearTimeout(timeoutId);
        }

        // Get and log response text
        text = await response.text();
        console.group('Response Data');
        console.log('Raw response:', text);
        console.log('Response length:', text.length);
        console.groupEnd();

        // Validate response
        if (!text.trim()) {
            console.error('Empty response received from server');
            throw new Error('Server returned an empty response. Please try again.');
        }

        // Clean response text
        const cleanText = text.trim().replace(/^\uFEFF/, '');

        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.toLowerCase().includes('application/json')) {
            console.error('Invalid content type:', contentType);
            throw new Error('Invalid response format: not JSON');
        }

        // Parse JSON with detailed error handling
        let data;
        try {
            data = JSON.parse(cleanText);
            console.group('Parsed Response');
            console.log('Parsed data:', data);
            console.log('Success status:', data.success);
            if (data.error) console.error('Error message:', data.error);
            console.groupEnd();
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Attempted to parse:', cleanText);
            throw new Error('Failed to parse server response. Please try again.');
        }

        // Validate response structure
        if (!data || typeof data !== 'object') {
            console.error('Invalid response structure:', data);
            throw new Error('Invalid response structure');
        }

        // Check for error in response
        if (!data.success) {
            throw new Error(data.error || 'Unknown server error');
        }

        // Show success message and reload
        await adminModal.alert({
            title: 'Success',
            message: data.message || 'Order confirmed and now preparing!',
            type: 'success'
        });
        location.reload();
        
        return data;
    } catch (error) {
        console.error('Error:', error);
        await adminModal.alert({
            title: 'Error',
            message: error.message || 'Failed to confirm order. Please try again.',
            type: 'error'
        });
        throw error; // Re-throw to be handled by caller
    }
}
