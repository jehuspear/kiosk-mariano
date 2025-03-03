<?php
session_start();
require_once 'database_admin.php';

if (!isset($_GET['order_id'])) {
    header('Location: point-of-sale.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = intval($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <link rel="stylesheet" href="Css-admin/receipt.css">
    <style>
        /* Additional styles for thicker fonts */
        .receipt-text {
            font-weight: 600;
        }
        .item-name {
            font-weight: 700;
        }
        .order-detail-label, 
        .order-detail-value, 
        .item-price, 
        .total-amount, 
        .payment-method-gcash, 
        .payment-method-cash, 
        .payment-change {
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt-container">
            <div id="receipt" class="receipt-text">
                Loading receipt...
            </div>
        </div>
        <div class="text-center mt-3 mb-4 no-print">
            <button class="btn btn-primary me-2" onclick="window.print()">Print Receipt</button>
            <a href="point-of-sale.php" class="btn btn-secondary">Back to POS</a>
        </div>
    </div>

    <script>
        async function loadReceipt() {
            try {
                console.log('Fetching receipt for order ID:', <?php echo $orderId; ?>);
                const response = await fetch(`get_receipt.php?order_id=<?php echo $orderId; ?>`);
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Invalid response format from server');
                }

                if (!response.ok) {
                    throw new Error(data.message || `Server error: ${response.status}`);
                }

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load receipt data');
                }

                console.log('Receipt data:', data);

                if (!data.receipt) {
                    throw new Error('Receipt data is missing');
                }

                const receipt = data.receipt;
                console.log('Rendering receipt:', receipt);
                console.log('Payment Method:', receipt.paymentMethod);
                console.log('Reference Number:', receipt.referenceNumber);

                // Format the receipt HTML
                const receiptHtml = `
                    <div class="print-receipt" style="width: 58mm;">
                        <img src="Images/logo/logo.png" alt="Logo" class="logo">
                        <div class="ticket-number-display">
                            Ticket #${receipt.ticketNumber}
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Order ID:</span>
                            <span class="order-detail-value">#${receipt.orderId.toString().padStart(8, '0')}</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Customer:</span>
                            <span class="order-detail-value">${receipt.customerName || 'N/A'}</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Date:</span>
                            <span class="order-detail-value">${new Date(receipt.dateTime).toLocaleString('en-US', {
                                month: '2-digit',
                                day: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            })}</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Cashier:</span>
                            <span class="order-detail-value">${receipt.staffName}</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Eating Option:</span>
                            <span class="order-detail-value">${receipt.eatingOption}</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Order Items:</span>
                            <span class="order-detail-value">
                                <ul class="order-items-list">
                                    ${receipt.items.map(item => `
                                        <li>
                                            <span class="item-name">${item.quantity} x [${item.temperature}] ${item.name} (${item.size})</span>
                                            <span class="item-price">₱${(item.price * item.quantity).toFixed(2)}</span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </span>
                        </div>
                        <div class="order-detail-item subtotal-section">
                            <span class="order-detail-label">Sub Total:</span>
                            <span class="order-detail-value">₱${receipt.subtotal.toFixed(2)}</span>
                        </div>
                        ${receipt.discountType ? `
                            <div class="order-detail-item">
                                <span class="order-detail-label">Discount Type:</span>
                                <span class="order-detail-value">${receipt.discountType}</span>
                            </div>
                            <div class="order-detail-item">
                                <span class="order-detail-label">Discount Amount:</span>
                                <span class="order-detail-value">₱${receipt.discountAmount.toFixed(2)}</span>
                            </div>
                        ` : ''}
                        <div class="order-detail-item total-section">
                            <span class="order-detail-label">Total Amount:</span>
                            <span class="order-detail-value total-amount">₱${receipt.total.toFixed(2)}</span>
                        </div>
                        <div class="order-detail-item payment-section">
                            <span class="order-detail-label">Payment Method:</span>
                            <span class="order-detail-value ${receipt.paymentMethod === 'GCash' ? 'payment-method-gcash' : 'payment-method-cash'}">
                                ${receipt.paymentMethod}
                            </span>
                        </div>
                        ${receipt.paymentMethod === 'GCash' ? (
                            console.log('Is GCash payment, ref number:', receipt.referenceNumber),
                            `
                            <div class="order-detail-item">
                                <span class="order-detail-label">Reference Number:</span>
                                <span class="order-detail-value">${receipt.referenceNumber}</span>
                            </div>
                        `) : (
                            console.log('Not GCash payment'),
                            ''
                        )}
                        ${receipt.paymentMethod === 'Cash' && receipt.cashAmount ? `
                            <div class="order-detail-item">
                                <span class="order-detail-label">Cash Paid:</span>
                                <span class="order-detail-value">₱${receipt.cashAmount.toFixed(2)}</span>
                            </div>
                            <div class="order-detail-item">
                                <span class="order-detail-label">Change:</span>
                                <span class="order-detail-value payment-change">₱${receipt.change.toFixed(2)}</span>
                            </div>
                        ` : ''}
                        <div class="footer">
                            <p>Thank you for your purchase!</p>
                            <p>Please keep this receipt for your records.</p>
                            <p>${new Date(receipt.dateTime).toLocaleString('en-US', {
                                month: '2-digit',
                                day: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: true
                            })}</p>
                        </div>
                        <div class="cutting-line"></div>
                    </div>
                `;

                // Update the receipt container
                const receiptContainer = document.getElementById('receipt');
                receiptContainer.innerHTML = receiptHtml;
                console.log('Receipt rendered successfully');
            } catch (error) {
                console.error('Error loading receipt:', error);
                document.getElementById('receipt').innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${error.message || 'Failed to load receipt. Please try again.'}
                        ${error.details ? `<br><small>${error.details}</small>` : ''}
                    </div>
                `;
            }
        }

        loadReceipt();
    </script>
</body>
</html>
