class POSReceiptPrinter {
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
            .replace(',', '')
            .replace(/\s+/g, ' ');
    }

    createReceiptTemplate(orderDetails, modalResult) {
        const orderDate = orderDetails.Order_DateTime ? 
            this.formatDate(orderDetails.Order_DateTime) : 
            this.formatDate(new Date());

        const items = orderDetails.items.split('<br>');
        const prices = orderDetails.itemPrices.split('<br>');
        
        return `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Receipt - Ticket #${orderDetails.ticketNumber}</title>
                <link rel="stylesheet" href="Css-admin/receipt.css">
                <style>
                    @page {
                        margin: 0;
                        size: 58mm auto;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                </style>
            </head>
            <body>
                <div class="receipt-container">
                    <img src="${this.baseUrl}Images/logo/logo.png" alt="Logo" class="receipt-logo">
                    <div class="ticket-number">
                        Ticket #${orderDetails.ticketNumber}
                    </div>
                    <div class="receipt-info">
                        <div class="receipt-info-row">
                            <span class="receipt-info-label">Order ID:</span>
                            <span>#${orderDetails.orderId.toString().padStart(8, '0')}</span>
                        </div>
                        <div class="receipt-info-row">
                            <span class="receipt-info-label">Date:</span>
                            <span>${orderDate}</span>
                        </div>
                        <div class="receipt-info-row">
                            <span class="receipt-info-label">Cashier:</span>
                            <span>${orderDetails.staffFirstName || ''}</span>
                        </div>
                        <div class="receipt-info-row">
                            <span class="receipt-info-label">Eating Option:</span>
                            <span>${orderDetails.eatingOption}</span>
                        </div>
                    </div>
                    <div class="receipt-items">
                        ${items.map((item, index) => `
                            <div class="receipt-item">
                                <div class="item-details">${item}</div>
                                <div class="item-price">${prices[index]}</div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="receipt-totals">
                        <div class="receipt-total-row subtotal">
                            <span>Sub Total:</span>
                            <span>₱${parseFloat(orderDetails.totalAmount).toFixed(2)}</span>
                        </div>
                        ${modalResult.discountType ? `
                            <div class="receipt-total-row discount">
                                <span>Discount (${modalResult.discountType} ${modalResult.discountPercent}%):</span>
                                <span>₱${modalResult.discountAmount.toFixed(2)}</span>
                            </div>
                        ` : ''}
                        <div class="receipt-total-row grand-total">
                            <span>Total Amount:</span>
                            <span>₱${modalResult.finalAmount.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="receipt-payment">
                        <div class="payment-row method">
                            <span>Payment Method:</span>
                            <span>${orderDetails.paymentMethod}</span>
                        </div>
                        ${orderDetails.paymentMethod === 'GCash' ? `
                            <div class="payment-row">
                                <span>Reference #:</span>
                                <span>${modalResult.referenceNumber}</span>
                            </div>
                        ` : `
                            <div class="payment-row">
                                <span>Cash Paid:</span>
                                <span>₱${modalResult.cashAmount.toFixed(2)}</span>
                            </div>
                            <div class="payment-row change">
                                <span>Change:</span>
                                <span>₱${modalResult.change.toFixed(2)}</span>
                            </div>
                        `}
                    </div>
                    <div class="receipt-footer">
                        <p>Thank you for your purchase!</p>
                        <p>Please keep this receipt for your records.</p>
                        <p>${orderDate}</p>
                    </div>
                    <div class="cutting-line"></div>
                </div>
            </body>
            </html>
        `;
    }

    showPreview(orderDetails, modalResult) {
        const previewContent = document.getElementById('printPreviewContent');
        if (!previewContent) {
            console.error('Print preview container not found');
            return;
        }

        // Add receipt content to preview
        previewContent.innerHTML = `
            <style>
                #printPreviewContent .receipt-container {
                    transform: scale(0.8);
                    transform-origin: top center;
                }
            </style>
            ${this.createReceiptTemplate(orderDetails, modalResult)}
        `;

        // Show preview modal
        const previewModal = new bootstrap.Modal(document.getElementById('printPreviewModal'));
        previewModal.show();

        // Handle print confirmation
        document.getElementById('confirmPrint').onclick = () => {
            this.printReceipt(orderDetails, modalResult);
            previewModal.hide();
        };
    }

    printReceipt(orderDetails, modalResult) {
        const printContent = this.createReceiptTemplate(orderDetails, modalResult);
        const printWindow = window.open('', '_blank', 'width=400,height=600');
        
        if (printWindow) {
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Wait for images to load before printing
            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
                setTimeout(() => printWindow.close(), 1000);
            };
        } else {
            console.error('Unable to open print window. Please check if pop-ups are blocked.');
            alert('Please allow pop-ups to print the receipt.');
        }
    }

    print(orderDetails, modalResult) {
        this.showPreview(orderDetails, modalResult);
    }
}

// Initialize printer
window.posReceiptPrinter = new POSReceiptPrinter();
