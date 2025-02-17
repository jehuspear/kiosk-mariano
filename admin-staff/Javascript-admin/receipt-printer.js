class ReceiptPrinter {
    constructor() {
        this.init();
    }

    init() {
        // Get base URL for resources
        this.baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
    }

    formatDate(date) {
        const options = { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        };
        return new Date(date).toLocaleString('en-US', options)
            .replace(',', '')  // Remove comma between date and time
            .replace(/\s+/g, ' '); // Normalize spaces
    }

    createPrintTemplate(orderDetails, modalResult) {
        // Use order date if available, otherwise use current date
        const orderDate = orderDetails.Order_DateTime ? 
            this.formatDate(orderDetails.Order_DateTime) : 
            this.formatDate(new Date());

        const items = orderDetails.items.split('<br>');
        const prices = orderDetails.itemPrices.split('<br>');
        
        // Calculate subtotal (original amount before discount)
        const subtotal = parseFloat(orderDetails.totalAmount);
        
        return `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Receipt - Ticket #${orderDetails.ticketNumber}</title>
                <style>
                    /* Base styles */
                    body {
                        margin: 0;
                        padding: 0;
                        background: white;
                        font-family: "Recursive Mono", monospace;

                        font-size: 9pt;
                        color: black;
                        width: 58mm;
                        line-height: 1.2;
                    }

                    .print-receipt {
                        padding: 2mm;
                        background: white;
                    }

                    /* Logo */
                    .print-receipt .logo {
                        width: 45mm;
                        height: auto;
                        margin: 0 auto 3mm;
                        display: block;
                        filter: invert(1) !important;
                        margin-top: 20mm; /* 2cm space */
                    }

                    /* Ticket number */
                    .print-receipt .ticket-number-display {
                        font-size: 21pt;
                        font-weight: 900;
                        text-align: center;
                        margin-bottom: 3mm;
                        padding: 1mm 0;
                        border-bottom: 1px solid black;
                    }

                    /* Order details */
                    .print-receipt .order-detail-item {
                        margin-bottom: 1.5mm;
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                    }

                    .print-receipt .order-detail-label {
                        font-weight: 800;
                        font-size: 9pt;
                    }

                    .print-receipt .order-detail-value {
                        text-align: right;
                        font-size: 9pt;
                        max-width: 60%;
                    }

                    /* Order items list */
                    .print-receipt .order-items-list {
                        list-style: none;
                        padding: 0;
                        margin: 2mm 0;
                    }

                    .print-receipt .order-items-list li {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 1mm;
                        font-size: 9pt;
                        line-height: 1.3;
                    }

                    .print-receipt .item-name {
                        flex: 1;
                        padding-right: 2mm;
                        white-space: normal;
                        word-wrap: break-word;
                    }

                    .print-receipt .item-price {
                        white-space: nowrap;
                        margin-left: 2mm;
                        font-weight: 800;
                    }

                    /* Subtotal section */
                    .print-receipt .subtotal-section {
                        margin-top: 3mm;
                        padding-top: 1.5mm;
                        border-top: 1px solid black;
                    }

                    /* Total section */
                    .print-receipt .total-section {
                        margin-top: 3mm;
                        padding-top: 1.5mm;
                        border-top: 1px solid black;
                        font-size: 12pt;
                    }

                    .print-receipt .total-section .order-detail-label,
                    .print-receipt .total-section .order-detail-value {
                        font-size: 10pt;
                        font-weight: 800;
                    }

                    /* Payment section */
                    .print-receipt .payment-section {
                        margin-top: 3mm;
                        padding-top: 1.5mm;
                        border-top: 1px solid black;
                    }

                    .print-receipt .payment-method-gcash,
                    .print-receipt .payment-method-cash {
                        font-weight: 800;
                    }

                    .print-receipt .payment-change {
                        font-weight: 800;
                    }

                    /* Footer */
                    .print-receipt .footer {
                        margin-top: 4mm;
                        text-align: center;
                        font-size: 8pt;
                        border-top: 1px solid black;
                        padding-top: 2mm;
                    }

                    .print-receipt .footer p {
                        margin: 1mm 0;
                        line-height: 1.3;
                    }

                    /* Cutting line */
                    .print-receipt .cutting-line {
                        margin-top: 20mm; /* 2cm space after footer content */
                        border-top: 1px solid black;
                        margin-bottom: 5mm; /* Some space after the cutting line */
                    }

                    @media print {
                        body {
                            width: 58mm !important;
                            margin: 0 !important;
                            padding: 0 !important;
                        }

                        .print-receipt {
                            width: 100% !important;
                            margin: 0 !important;
                            padding: 3mm !important;
                           
                        }

                        * {
                            color: black !important;
                            background: none !important;
                            box-shadow: none !important;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                    }

                    @page {
                        margin: 0;
                        size: 58mm auto;
                    }
                </style>
            </head>
            <body>
                <div class="print-receipt">
                    <img src="${this.baseUrl}Images/logo/logo.png" alt="Logo" class="logo">
                    <div class="ticket-number-display">
                        Ticket #${orderDetails.ticketNumber}
                    </div>
                    <div class="order-detail-item">
                        <span class="order-detail-label">Order ID:</span>
                        <span class="order-detail-value">#${orderDetails.orderId.toString().padStart(8, '0')}</span>
                    </div>
                    <div class="order-detail-item">
                        <span class="order-detail-label">Date:</span>
                        <span class="order-detail-value">${orderDate}</span>
                    </div>
                    <div class="order-detail-item">
                        <span class="order-detail-label">Cashier:</span>
                        <span class="order-detail-value">${orderDetails.staffFirstName || ''}</span>
                    </div>
                    <div class="order-detail-item">
                        <span class="order-detail-label">Eating Option:</span>
                        <span class="order-detail-value">${orderDetails.eatingOption}</span>
                    </div>
                    <div class="order-detail-item">
                        <span class="order-detail-label">Order Items:</span>
                        <span class="order-detail-value">
                            <ul class="order-items-list">
                                ${items.map((item, index) => `
                                    <li>
                                        <span class="item-name">${item}</span>
                                        <span class="item-price">${prices[index]}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </span>
                    </div>
                    <div class="order-detail-item subtotal-section">
                        <span class="order-detail-label">Sub Total:</span>
                        <span class="order-detail-value">₱${subtotal.toFixed(2)}</span>
                    </div>
                    ${modalResult.discountType ? `
                        <div class="order-detail-item">
                            <span class="order-detail-label">Discount Type:</span>
                            <span class="order-detail-value">${modalResult.discountType} (${modalResult.discountPercent}%)</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Discount Amount:</span>
                            <span class="order-detail-value">₱${modalResult.discountAmount.toFixed(2)}</span>
                        </div>
                    ` : ''}
                    <div class="order-detail-item total-section">
                        <span class="order-detail-label">Total Amount:</span>
                        <span class="order-detail-value total-amount">₱${modalResult.finalAmount.toFixed(2)}</span>
                    </div>
                    <div class="order-detail-item payment-section">
                        <span class="order-detail-label">Payment Method:</span>
                        <span class="order-detail-value ${orderDetails.paymentMethod === 'GCash' ? 'payment-method-gcash' : 'payment-method-cash'}">
                            ${orderDetails.paymentMethod}
                        </span>
                    </div>
                    ${orderDetails.paymentMethod === 'GCash' ? `
                        <div class="order-detail-item">
                            <span class="order-detail-label">Reference Number:</span>
                            <span class="order-detail-value">${modalResult.referenceNumber}</span>
                        </div>
                    ` : `
                        <div class="order-detail-item">
                            <span class="order-detail-label">Cash Paid:</span>
                            <span class="order-detail-value">₱${modalResult.cashAmount.toFixed(2)}</span>
                        </div>
                        <div class="order-detail-item">
                            <span class="order-detail-label">Change:</span>
                            <span class="order-detail-value payment-change">₱${modalResult.change.toFixed(2)}</span>
                        </div>
                    `}
                    <div class="footer">
                        <p>Thank you for your purchase!</p>
                        <p>Please keep this receipt for your records.</p>
                        <p>${orderDate}</p>
                    </div>
                    <div class="cutting-line"></div>
                </div>
                <script>
                    window.onload = function() {
                        // Wait for logo to load
                        const logo = document.querySelector('.logo');
                        if (logo.complete) {
                            window.print();
                            setTimeout(() => window.close(), 500);
                        } else {
                            logo.onload = function() {
                                window.print();
                                setTimeout(() => window.close(), 500);
                            };
                            logo.onerror = function() {
                                // Print even if logo fails to load
                                window.print();
                                setTimeout(() => window.close(), 500);
                            };
                        }
                    };
                </script>
            </body>
            </html>
        `;
    }

    print(orderDetails, modalResult) {
        // Calculate center position
        const width = 400;
        const height = 600;
        const left = (window.screen.width - width) / 2;
        const top = (window.screen.height - height) / 2;

        // Open a new window for printing at center position
        const printWindow = window.open('', '_blank', 
            `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`);
        if (printWindow) {
            printWindow.document.write(this.createPrintTemplate(orderDetails, modalResult));
            printWindow.document.close();
        } else {
            console.error('Unable to open print window. Please check if pop-ups are blocked.');
            alert('Please allow pop-ups to print the receipt.');
        }
    }
}

// Initialize printer
window.receiptPrinter = new ReceiptPrinter();
