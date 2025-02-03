class AdminModal {
    constructor() {
        this.init();
    }

    init() {
        // Create modal HTML
        const modalHTML = `
            <div class="admin-modal-overlay" id="adminModal">
                <div class="admin-modal">
                    <div class="admin-modal-header">
                        <h5 class="admin-modal-title"></h5>
                        <button class="admin-modal-close">&times;</button>
                    </div>
                    <div class="admin-modal-body">
                        <div class="admin-modal-icon"></div>
                        <div class="admin-modal-message"></div>
                        <div class="order-details"></div>
                    </div>
                    <div class="admin-modal-footer"></div>
                </div>
            </div>
        `;

        // Add modal to document
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Store modal elements
        this.modal = document.getElementById('adminModal');
        this.modalContainer = this.modal.querySelector('.admin-modal');
        this.title = this.modal.querySelector('.admin-modal-title');
        this.icon = this.modal.querySelector('.admin-modal-icon');
        this.message = this.modal.querySelector('.admin-modal-message');
        this.orderDetails = this.modal.querySelector('.order-details');
        this.footer = this.modal.querySelector('.admin-modal-footer');
        this.closeBtn = this.modal.querySelector('.admin-modal-close');

        // Add close button event
        this.closeBtn.addEventListener('click', () => this.hide());

        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .payment-method-gcash {
                color: #0066FF;
                font-weight: bold;
            }
            .payment-method-cash {
                color: #28a745;
                font-weight: bold;
            }
            .payment-input {
                margin-top: 10px;
                text-align: center;
            }
            .payment-input input {
                width: 290px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                text-align: center;
            }
            .payment-input input.error {
                border-color: #dc3545;
            }
            .payment-change {
                margin-top: 5px;
                font-weight: bold;
                color: #28a745;
            }
            .validation-message {
                color: #dc3545;
                margin-bottom: 10px;
                text-align: center;
                min-height: 20px;
                text-aligh:left;
            }
            .admin-modal-btn-confirm:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        `;
        document.head.appendChild(style);
    }

    show() {
        this.modal.style.display = 'flex';
    }

    hide() {
        this.modal.style.display = 'none';
        // Remove any special modal classes
        this.modalContainer.classList.remove('cancel', 'confirm');
        // Clear order details
        this.orderDetails.innerHTML = '';
    }

    setOrderDetails(order, isCancel = false) {
        if (!order) {
            this.orderDetails.innerHTML = '';
            return;
        }

        // Log incoming order details
        console.group('Incoming Order Details');
        console.log('Raw Order Data:', order);
        console.log('Is Cancel Operation:', isCancel);
        console.groupEnd();

        // Split items and prices into arrays
        const items = order.items.split('<br>');
        const prices = order.itemPrices.split('<br>');

        // Calculate subtotal
        const subtotal = parseFloat(order.totalAmount);

        // Log processed order data
        console.group('Processed Order Data');
        console.log('Items:', items);
        console.log('Prices:', prices);
        console.log('Subtotal:', subtotal);
        console.log('Payment Method:', order.paymentMethod);
        console.log('Eating Option:', order.eatingOption);
        console.log('Ticket Number:', order.ticketNumber);
        console.groupEnd();

        // For cancel orders, we only show basic order details
        if (isCancel) {
            this.orderDetails.innerHTML = `
                <div class="ticket-number-display">
                    Ticket #${order.ticketNumber}
                </div>
                <div class="order-detail-item">
                    <span class="order-detail-label">Eating Option:</span>
                    <span class="order-detail-value">${order.eatingOption}</span>
                </div>
                <div class="order-detail-item">
                    <span class="order-detail-label">Order Items:</span>
                    <span class="order-detail-value">
                        <ul class="order-items-list">
                            ${items.map((item, index) => `
                                <li>
                                    ${item}
                                    <div class="item-price">${prices[index]}</div>
                                </li>
                            `).join('')}
                        </ul>
                    </span>
                </div>
                <div class="order-detail-item">
                    <span class="order-detail-label">Total Amount:</span>
                    <span class="order-detail-value total-amount">₱${subtotal.toFixed(2)}</span>
                </div>
                <div class="order-detail-item">
                    <span class="order-detail-label">Payment Method:</span>
                    <span class="order-detail-value ${order.paymentMethod === 'GCash' ? 'payment-method-gcash' : 'payment-method-cash'}">${order.paymentMethod}</span>
                </div>
            `;
            return;
        }

        // For confirm orders, show full details with input fields
        const paymentMethodClass = order.paymentMethod === 'GCash' ? 'payment-method-gcash' : 'payment-method-cash';
        const paymentInputHtml = order.paymentMethod === 'GCash' 
            ? `<div class="payment-input">
             <span class="order-detail-value ${paymentMethodClass}">${order.paymentMethod}</span>
                <input type="text" id="referenceNumber" class="form-control" maxlength="6" placeholder="Enter last 6 digits of Reference Number" required>
               </div>`
            : `<div class="payment-input">
             <span class="order-detail-value ${paymentMethodClass}">${order.paymentMethod}</span>
                <input type="number" id="cashAmount" class="form-control" min="0" placeholder="Enter Cash Paid Amount" required>
                <div class="payment-change">Change: ₱0.00</div>
               </div>`;

        this.orderDetails.innerHTML = `
            <div class="ticket-number-display">
                Ticket #${order.ticketNumber}
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Eating Option:</span>
                <span class="order-detail-value">${order.eatingOption}</span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Order Items:</span>
                <span class="order-detail-value">
                    <ul class="order-items-list">
                        ${items.map((item, index) => `
                            <li>
                                ${item}
                                <div class="item-price">${prices[index]}</div>
                            </li>
                        `).join('')}
                    </ul>
                </span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Sub Total:</span>
                <span class="order-detail-value subtotal-amount">₱${subtotal.toFixed(2)}</span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Discount Type:</span>
                <div class="discount-type-container">
                    <select id="discountType" class="form-control">
                        <option value="">No Discount</option>
                        <option value="Senior">Senior Citizen</option>
                        <option value="PWD">PWD</option>
                        <option value="Student">Student</option>
                        <option value="Custom">Custom</option>
                    </select>
                    <input type="text" id="customDiscountType" class="form-control mt-2" 
                           placeholder="Custom Discount" style="display: none;">
                </div>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Discount (%):</span>
                <input type="number" id="discountPercent" class="form-control" min="0" max="100" value="0" readonly>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Discount Amount:</span>
                <span class="order-detail-value discount-amount">₱0.00</span>
            </div>
            <div class="order-detail-item total-section">
                <span class="order-detail-label">Total Amount:</span>
                <span class="order-detail-value total-amount">₱${subtotal.toFixed(2)}</span>
            </div>
            <div class="order-detail-item payment-section">
                <span class="order-detail-label">Payment Method:</span>
               
                ${paymentInputHtml}
            </div>
        `;

        // Initialize elements after they're created
        const discountType = document.getElementById('discountType');
        const customDiscountType = document.getElementById('customDiscountType');
        const discountPercent = document.getElementById('discountPercent');
        const cashAmount = document.getElementById('cashAmount');
        const referenceNumber = document.getElementById('referenceNumber');

        // Set initial state
        discountPercent.readOnly = true;
        customDiscountType.style.display = 'none';

        const updateDiscount = () => {
            const percent = parseFloat(discountPercent.value) || 0;
            const discountAmount = (subtotal * percent) / 100;
            const finalAmount = subtotal - discountAmount;

            document.querySelector('.discount-amount').textContent = `₱${discountAmount.toFixed(2)}`;
            document.querySelector('.total-amount').textContent = `₱${finalAmount.toFixed(2)}`;

            // Update change if cash payment
            if (cashAmount) {
                const paid = parseFloat(cashAmount.value) || 0;
                const change = paid - finalAmount;
                document.querySelector('.payment-change').textContent = `Change: ₱${Math.max(0, change).toFixed(2)}`;
            }
        };

        // Create validation function in modal scope
        const validateFields = () => {
            const discountType = document.getElementById('discountType');
            const customDiscountType = document.getElementById('customDiscountType');
            const referenceNumber = document.getElementById('referenceNumber');
            const cashAmount = document.getElementById('cashAmount');
            const totalAmount = parseFloat(document.querySelector('.total-amount').textContent.replace('₱', ''));
            const yesBtn = document.getElementById('modalYesBtn');

            let isValid = true;
            let message = '';

            // Clear previous error states
            if (referenceNumber) referenceNumber.classList.remove('error');
            if (cashAmount) cashAmount.classList.remove('error');
            if (customDiscountType) customDiscountType.classList.remove('error');

            // Validate custom discount type if selected
            if (discountType.value === 'Custom' && !customDiscountType.value.trim()) {
                isValid = false;
                message = 'Please enter a custom discount type';
                customDiscountType.classList.add('error');
            }

            // Only validate GCash reference number if it's a GCash payment
            if (referenceNumber && !cashAmount) {
                if (!referenceNumber.value || referenceNumber.value.length !== 6) {
                    isValid = false;
                    message = 'Please enter a valid 6-digit GCash reference number';
                    referenceNumber.classList.add('error');
                }
            }

            // Only validate cash amount if it's a cash payment
            if (cashAmount && !referenceNumber) {
                if (!cashAmount.value || parseFloat(cashAmount.value) < totalAmount) {
                    isValid = false;
                    message = 'Please enter a valid cash amount that covers the total';
                    cashAmount.classList.add('error');
                }
            }

            // Update validation message and button state
            const validationMessage = document.querySelector('.validation-message');
            if (validationMessage) {
                validationMessage.textContent = message;
            }
            if (yesBtn) {
                yesBtn.disabled = !isValid;
            }

            return isValid;
        };

        // Add event listeners after elements are initialized
        setTimeout(() => {
            discountType.addEventListener('change', (e) => {
                if (e.target.value === '') {
                    // No Discount selected
                    discountPercent.value = '0';
                    discountPercent.readOnly = true;
                    customDiscountType.style.display = 'none';
                } else if (e.target.value === 'Senior' || e.target.value === 'PWD') {
                    discountPercent.value = '20';
                    discountPercent.readOnly = false;
                    customDiscountType.style.display = 'none';
                } else if (e.target.value === 'Student') {
                    discountPercent.value = '10';
                    discountPercent.readOnly = false;
                    customDiscountType.style.display = 'none';
                } else if (e.target.value === 'Custom') {
                    discountPercent.value = '0';
                    discountPercent.readOnly = false;
                    customDiscountType.style.display = 'block';
                }
                updateDiscount();
                validateFields();
            });

            discountPercent.addEventListener('input', (e) => {
                // Ensure value doesn't exceed 100
                let value = parseFloat(e.target.value);
                if (value > 100) {
                    e.target.value = '100';
                    value = 100;
                } else if (value < 0) {
                    e.target.value = '0';
                    value = 0;
                }
                updateDiscount();
                validateFields();
            });

            // Add cash amount listener if it exists
            if (cashAmount) {
                cashAmount.addEventListener('input', () => {
                    updateDiscount();
                    validateFields();
                });
            }

            // Add reference number listener if it exists
            if (referenceNumber) {
                referenceNumber.addEventListener('input', (e) => {
                    // Only allow numbers
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                    // Limit to 6 digits
                    if (e.target.value.length > 6) {
                        e.target.value = e.target.value.slice(0, 6);
                    }
                    validateFields();
                });
            }

            // Initial validation after a short delay to ensure DOM is ready
            setTimeout(validateFields, 100);

            // Debug logging
            console.log('Modal elements initialized:', {
                discountType: !!discountType,
                customDiscountType: !!customDiscountType,
                discountPercent: !!discountPercent,
                cashAmount: !!cashAmount,
                referenceNumber: !!referenceNumber
            });
        }, 0);
    }

    confirm(options) {
        return new Promise((resolve, reject) => {
            this.title.textContent = options.title || 'Confirm';
            this.icon.innerHTML = '<i class="fas fa-question-circle"></i>';
            this.icon.className = 'admin-modal-icon confirm';
            this.message.textContent = options.message || 'Are you sure?';

            // Check if this is a cancel operation
            const isCancel = options.title?.toLowerCase().includes('cancel');
            
            // Set order details if provided
            if (options.order) {
                this.setOrderDetails(options.order, isCancel);
            }

            // Add appropriate class based on modal type
            if (isCancel) {
                this.modalContainer.classList.add('cancel');
                this.icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                this.icon.className = 'admin-modal-icon warning';
            } else if (options.title?.toLowerCase().includes('confirm')) {
                this.modalContainer.classList.add('confirm');
                this.icon.innerHTML = '<i class="fas fa-question-circle"></i>';
                this.icon.className = 'admin-modal-icon confirm';
            }

            // Create buttons and validation message
            this.footer.innerHTML = isCancel 
                ? `<button class="admin-modal-btn admin-modal-btn-secondary" id="modalNoBtn">No</button>
                   <button class="admin-modal-btn admin-modal-btn-confirm" id="modalYesBtn">Yes</button>`
                : `<div class="validation-message"></div>
                   <button class="admin-modal-btn admin-modal-btn-secondary" id="modalNoBtn">No</button>
                   <button class="admin-modal-btn admin-modal-btn-confirm" id="modalYesBtn" disabled>Yes</button>`;

            const noBtn = this.footer.querySelector('#modalNoBtn');
            const yesBtn = this.footer.querySelector('#modalYesBtn');
            const validationMessage = this.footer.querySelector('.validation-message');

            const cleanup = () => {
                noBtn.removeEventListener('click', handleNo);
                yesBtn.removeEventListener('click', handleYes);
            };

            const handleNo = () => {
                this.hide();
                cleanup();
                resolve(false);
            };

            const handleYes = async () => {
                try {
                    if (!isCancel) {
                        // Log initial order details
                        console.group('Order Details Before Processing');
                        console.log('Original Order:', options.order);
                        console.groupEnd();

                        const discountType = document.getElementById('discountType');
                        const customDiscountType = document.getElementById('customDiscountType');
                        const referenceNumber = document.getElementById('referenceNumber');
                        const cashAmount = document.getElementById('cashAmount');

                        // Get and validate total amount
                        const finalTotal = parseFloat(document.querySelector('.total-amount').textContent.replace('₱', ''));
                        if (isNaN(finalTotal) || finalTotal < 0) {
                            throw new Error('Invalid total amount');
                        }

                        // Initialize payment values
                        let cashValue = 0;
                        let referenceNumberValue = '';
                        let changeValue = 0;

                        // Get payment values based on payment method
                        if (cashAmount) {
                            cashValue = parseFloat(cashAmount.value) || 0;
                            if (cashValue <= 0) {
                                throw new Error('Cash amount must be greater than 0');
                            }
                            if (cashValue < finalTotal) {
                                throw new Error('Cash amount must cover the total amount');
                            }
                            changeValue = cashValue - finalTotal;
                        } else if (referenceNumber) {
                            referenceNumberValue = referenceNumber.value.trim();
                            if (!referenceNumberValue) {
                                throw new Error('Reference number is required');
                            }
                            if (referenceNumberValue.length !== 6) {
                                throw new Error('Reference number must be 6 digits');
                            }
                        }

                        // Log all form values
                        console.group('Form Values Before Submission');
                        console.log('Discount Type:', discountType.value);
                        console.log('Custom Discount Type:', customDiscountType.value);
                        console.log('Final Total:', finalTotal);
                        console.log('Cash Amount:', cashValue);
                        console.log('Reference Number:', referenceNumberValue);
                        console.log('Change:', changeValue);
                        console.groupEnd();

                        // Get discount values
                        const actualDiscountType = discountType.value === 'Custom' ? customDiscountType.value : discountType.value;
                        const discountPercentValue = parseFloat(document.getElementById('discountPercent').value) || 0;
                        const discountAmountValue = parseFloat(document.querySelector('.discount-amount').textContent.replace('₱', '')) || 0;

                        // Log calculated values
                        console.group('Calculated Values');
                        console.log('Actual Discount Type:', actualDiscountType);
                        console.log('Discount Percent:', discountPercentValue);
                        console.log('Discount Amount:', discountAmountValue);
                        console.groupEnd();

                        // Create result object for confirm order
                        const result = {
                            confirmed: true,
                            discountType: actualDiscountType,
                            discountPercent: discountPercentValue,
                            discountAmount: discountAmountValue,
                            finalAmount: finalTotal,
                            referenceNumber: referenceNumberValue,
                            cashAmount: cashValue,
                            change: changeValue,
                            // Include original order details
                            orderDetails: {
                                ticketNumber: options.order.ticketNumber,
                                eatingOption: options.order.eatingOption,
                                items: options.order.items,
                                itemPrices: options.order.itemPrices,
                                paymentMethod: options.order.paymentMethod,
                                originalTotal: options.order.totalAmount
                            }
                        };

                        // Log final result object
                        console.group('Final Result Object');
                        console.log('Result:', result);
                        console.log('JSON String:', JSON.stringify(result));
                        console.groupEnd();

                        // Clean up and resolve
                        this.hide();
                        cleanup();
                        resolve(result);
                    } else {
                        // For cancel order, just resolve with true
                        this.hide();
                        cleanup();
                        resolve(true);
                    }
                } catch (error) {
                    console.error('Error in modal:', error);
                    validationMessage.textContent = error.message;
                    yesBtn.disabled = true;
                    setTimeout(() => {
                        validationMessage.textContent = '';
                        yesBtn.disabled = false;
                    }, 3000);
                }
            };

            noBtn.addEventListener('click', handleNo);
            yesBtn.addEventListener('click', handleYes);

            this.show();
        });
    }

    alert(options) {
        return new Promise((resolve) => {
            this.title.textContent = options.title || 'Alert';
            
            const iconClass = options.type === 'success' ? 'check-circle' : 'times-circle';
            this.icon.innerHTML = `<i class="fas fa-${iconClass}"></i>`;
            this.icon.className = `admin-modal-icon ${options.type || 'success'}`;
            
            this.message.textContent = options.message || '';

            // Set order details if provided
            if (options.order) {
                this.setOrderDetails(options.order, options.title?.toLowerCase().includes('cancel'));
            }

            // Add appropriate class based on alert type
            if (options.title?.toLowerCase().includes('cancel')) {
                this.modalContainer.classList.add('cancel');
            } else if (options.type === 'success') {
                this.modalContainer.classList.add('confirm');
            }

            // Create OK button
            this.footer.innerHTML = `
                <button class="admin-modal-btn admin-modal-btn-confirm">OK</button>
            `;

            const okBtn = this.footer.querySelector('.admin-modal-btn');

            const handleOk = () => {
                this.hide();
                okBtn.removeEventListener('click', handleOk);
                resolve();
            };

            okBtn.addEventListener('click', handleOk);

            this.show();
        });
    }
}

// Initialize modal
const adminModal = new AdminModal();
