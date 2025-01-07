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

    setOrderDetails(order) {
        if (!order) {
            this.orderDetails.innerHTML = '';
            return;
        }

        this.orderDetails.innerHTML = `
            <div class="ticket-number-display">
                Ticket #${order.ticketNumber}
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Payment Method:</span>
                <span class="order-detail-value">${order.paymentMethod}</span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Eating Option:</span>
                <span class="order-detail-value">${order.eatingOption}</span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Order Items:</span>
                <span class="order-detail-value">
                    <ul class="order-items-list">
                        ${order.items.split('<br>').map(item => `<li>${item}</li>`).join('')}
                    </ul>
                </span>
            </div>
            <div class="order-detail-item">
                <span class="order-detail-label">Total Amount:</span>
                <span class="order-detail-value total-amount">₱${parseFloat(order.totalAmount).toFixed(2)}</span>
            </div>
        `;
    }

    confirm(options) {
        return new Promise((resolve) => {
            this.title.textContent = options.title || 'Confirm';
            this.icon.innerHTML = '<i class="fas fa-question-circle"></i>';
            this.icon.className = 'admin-modal-icon confirm';
            this.message.textContent = options.message || 'Are you sure?';

            // Set order details if provided
            if (options.order) {
                this.setOrderDetails(options.order);
            }

            // Add appropriate class based on modal type
            if (options.title?.toLowerCase().includes('cancel')) {
                this.modalContainer.classList.add('cancel');
                this.icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                this.icon.className = 'admin-modal-icon warning';
            } else if (options.title?.toLowerCase().includes('confirm')) {
                this.modalContainer.classList.add('confirm');
                this.icon.innerHTML = '<i class="fas fa-question-circle"></i>';
                this.icon.className = 'admin-modal-icon confirm';
            }

            // Create buttons
            this.footer.innerHTML = `
                <button class="admin-modal-btn admin-modal-btn-secondary" id="modalNoBtn">No</button>
                <button class="admin-modal-btn admin-modal-btn-confirm" id="modalYesBtn">Yes</button>
            `;

            const noBtn = this.footer.querySelector('#modalNoBtn');
            const yesBtn = this.footer.querySelector('#modalYesBtn');

            const cleanup = () => {
                noBtn.removeEventListener('click', handleNo);
                yesBtn.removeEventListener('click', handleYes);
            };

            const handleNo = () => {
                this.hide();
                cleanup();
                resolve(false);
            };

            const handleYes = () => {
                this.hide();
                cleanup();
                resolve(true);
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
                this.setOrderDetails(options.order);
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
