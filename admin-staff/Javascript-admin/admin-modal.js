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
                        <div class="order-details modal-order-details"></div>
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
    }

    show() {
        this.modal.style.display = 'flex';
    }

    hide() {
        this.modal.style.display = 'none';
        this.modalContainer.classList.remove('cancel', 'confirm');
        this.orderDetails.innerHTML = '';
    }

    setOrderDetails(order, isCancel = false) {
        if (!order || !order.items || !order.itemPrices || !order.totalAmount) {
            this.orderDetails.innerHTML = '';
            return;
        }

        // Split items and prices into arrays
        const items = order.items.split('<br>');
        const prices = order.itemPrices.split('<br>');

        // Calculate subtotal
        const subtotal = parseFloat(order.totalAmount);

        // For cancel orders, we only show basic order details
        if (isCancel) {
            this.orderDetails.innerHTML = `
                <div class="ticket-number-display">
                    Ticket #${order.ticketNumber}
                </div>
                <div class="order-detail-item">
                    <span class="order-detail-label">Order ID:</span>
                    <span class="order-detail-value">#${(order.orderId || '').toString().padStart(8, '0')}</span>
                </div>
                <div class="order-detail-item">
                    <span class="order-detail-label">Date:</span>
                    <span class="order-detail-value">${order.date}</span>
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
                    <span class="order-detail-value total-amount">₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
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
                <input type="text" id="referenceNumber" class="form-control" maxlength="6" placeholder="Enter last 6 digits of Reference Number" autocomplete="off" required>
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
                <span class="order-detail-label">Order ID:</span>
                <span class="order-detail-value">#${(order.orderId || '').toString().padStart(8, '0')}</span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Date:</span>
                <span class="order-detail-value">${order.date}</span>
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
                <span class="order-detail-value subtotal-amount">₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
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
                <span class="order-detail-value total-amount">₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
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

            document.querySelector('.discount-amount').textContent = `₱${discountAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.querySelector('.total-amount').textContent = `₱${finalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Update change if cash payment
            if (cashAmount) {
                const paid = parseFloat(cashAmount.value) || 0;
                const change = paid - finalAmount;
                document.querySelector('.payment-change').textContent = `Change: ₱${Math.max(0, change).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
        };

        // Create validation function in modal scope
        const validateFields = () => {
            const discountType = document.getElementById('discountType');
            const customDiscountType = document.getElementById('customDiscountType');
            const referenceNumber = document.getElementById('referenceNumber');
            const cashAmount = document.getElementById('cashAmount');
            const customerName = document.getElementById('customerName');
            const totalAmount = parseFloat(document.querySelector('.total-amount').textContent.replace('₱', '').replace(/,/g, ''));
            const yesBtn = document.getElementById('modalYesBtn');

            let isValid = true;
            let message = '';

            // Clear previous error states
            if (referenceNumber) referenceNumber.classList.remove('error');
            if (cashAmount) cashAmount.classList.remove('error');
            if (customDiscountType) customDiscountType.classList.remove('error');
            if (customerName) customerName.classList.remove('error');

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
                    message = 'Please enter the last 6-digit GCash reference number';
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
        }, 0);
    }

    confirm(options) {
        return new Promise((resolve) => {
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

            const cleanup = () => {
                noBtn.removeEventListener('click', handleNo);
                yesBtn.removeEventListener('click', handleYes);
            };

            const handleNo = () => {
                this.hide();
                cleanup();
                resolve({ confirmed: false });
            };

            const handleYes = async () => {
                if (isCancel) {
                    this.hide();
                    cleanup();
                    resolve({ confirmed: true });
                    return;
                }

                // Get all form values
                const discountType = document.getElementById('discountType');
                const customDiscountType = document.getElementById('customDiscountType');
                const discountPercent = document.getElementById('discountPercent');
                const referenceNumber = document.getElementById('referenceNumber');
                const cashAmount = document.getElementById('cashAmount');
                const totalAmount = parseFloat(document.querySelector('.total-amount').textContent.replace('₱', '').replace(/,/g, ''));
                const discountAmount = parseFloat(document.querySelector('.discount-amount').textContent.replace('₱', '').replace(/,/g, ''));
                const change = cashAmount ? parseFloat(cashAmount.value || 0) - totalAmount : 0;

                // Create a temporary result object without customer name
                const tempResult = {
                    confirmed: true,
                    discountType: discountType.value === 'Custom' ? customDiscountType.value : discountType.value,
                    discountPercent: parseFloat(discountPercent.value) || 0,
                    discountAmount: discountAmount,
                    finalAmount: totalAmount,
                    referenceNumber: referenceNumber ? referenceNumber.value : '',
                    cashAmount: cashAmount ? parseFloat(cashAmount.value) || 0 : 0,
                    change: Math.max(0, change)
                };

                // Hide the current modal
                this.hide();
                cleanup();

                // Create and show the customer name modal
                const customerNameModal = document.createElement('div');
                customerNameModal.className = 'admin-modal-overlay';
                customerNameModal.id = 'customerNameModal';
                customerNameModal.style.display = 'flex';
                customerNameModal.innerHTML = `
                    <div class="admin-modal">
                        <div class="admin-modal-header">
                            <h5 class="admin-modal-title">Order Confirmation</h5>
                            <button class="admin-modal-close">&times;</button>
                        </div>
                        <div class="admin-modal-body">
                            <div class="admin-modal-icon confirm">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="admin-modal-message">Please enter the customer name to finalize the order.</div>
                            <div class="customer-name-input" style="margin-top: 20px;">
                                <input type="text" id="customerNameInput" class="form-control" placeholder="Enter customer name" required>
                                <div class="validation-message" style="color: red; margin-top: 5px;"></div>
                            </div>
                        </div>
                        <div class="admin-modal-footer">
                            <button class="admin-modal-btn admin-modal-btn-secondary" id="cancelBtn">Cancel</button>
                            <button class="admin-modal-btn admin-modal-btn-confirm" id="confirmBtn">Confirm Order</button>
                        </div>
                    </div>
                `;

                document.body.appendChild(customerNameModal);

                const customerNameInput = document.getElementById('customerNameInput');
                const confirmBtn = document.getElementById('confirmBtn');
                const cancelBtn = document.getElementById('cancelBtn');
                const closeBtn = customerNameModal.querySelector('.admin-modal-close');
                const validationMessage = customerNameModal.querySelector('.validation-message');

                // Focus on the input field
                setTimeout(() => customerNameInput.focus(), 100);

                // Validate customer name
                const validateCustomerName = () => {
                    if (!customerNameInput.value.trim()) {
                        validationMessage.textContent = 'Please enter a customer name';
                        confirmBtn.disabled = true;
                        return false;
                    } else {
                        validationMessage.textContent = '';
                        confirmBtn.disabled = false;
                        return true;
                    }
                };

                // Add event listeners
                customerNameInput.addEventListener('input', validateCustomerName);
                confirmBtn.disabled = true; // Initially disabled

                // Handle confirm button click
                confirmBtn.addEventListener('click', () => {
                    if (validateCustomerName()) {
                        // Add customer name to the result
                        const finalResult = {
                            ...tempResult,
                            customerName: customerNameInput.value.trim()
                        };

                        // Print receipt
                        if (options.order && window.receiptPrinter) {
                            const orderWithId = { ...options.order, orderId: options.order.orderId };
                            window.receiptPrinter.print(orderWithId, finalResult);
                        }

                        // Debug log the result
                        console.log('Final Modal Result:', finalResult);

                        // Remove the modal
                        document.body.removeChild(customerNameModal);

                        // Resolve the promise with the final result
                        resolve(finalResult);
                    }
                });

                // Handle cancel button click
                cancelBtn.addEventListener('click', () => {
                    document.body.removeChild(customerNameModal);
                    resolve({ confirmed: false });
                });

                // Handle close button click
                closeBtn.addEventListener('click', () => {
                    document.body.removeChild(customerNameModal);
                    resolve({ confirmed: false });
                });
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
