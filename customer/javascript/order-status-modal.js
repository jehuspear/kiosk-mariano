class CustomModal {
    constructor() {
        this.init();
    }

    init() {
        // Create modal elements
        const modalHTML = `
            <div class="modal-overlay" id="customModal">
                <div class="modal-container">
                    <div class="modal-icon"></div>
                    <div class="modal-title"></div>
                    <div class="modal-message"></div>
                    <div class="modal-buttons"></div>
                </div>
            </div>
        `;

        // Add modal to document
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Store modal elements
        this.modal = document.getElementById('customModal');
        this.modalIcon = this.modal.querySelector('.modal-icon');
        this.modalTitle = this.modal.querySelector('.modal-title');
        this.modalMessage = this.modal.querySelector('.modal-message');
        this.modalButtons = this.modal.querySelector('.modal-buttons');
    }

    show() {
        this.modal.style.display = 'flex';
    }

    hide() {
        this.modal.style.display = 'none';
    }

    confirm(title, message) {
        return new Promise((resolve) => {
            this.modalIcon.innerHTML = '<i class="fas fa-question-circle"></i>';
            this.modalIcon.className = 'modal-icon';
            this.modalTitle.textContent = title;
            this.modalMessage.innerHTML = message;
            
            this.modalButtons.innerHTML = `
                <button class="modal-btn modal-btn-confirm">Confirm</button>
                <button class="modal-btn modal-btn-cancel">Cancel</button>
            `;

            const confirmBtn = this.modalButtons.querySelector('.modal-btn-confirm');
            const cancelBtn = this.modalButtons.querySelector('.modal-btn-cancel');

            const handleConfirm = () => {
                this.hide();
                resolve(true);
                cleanup();
            };

            const handleCancel = () => {
                this.hide();
                resolve(false);
                cleanup();
            };

            const cleanup = () => {
                confirmBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };

            confirmBtn.addEventListener('click', handleConfirm);
            cancelBtn.addEventListener('click', handleCancel);

            this.show();
        });
    }

    alert(title, message, type = 'success') {
        return new Promise((resolve) => {
            const icon = type === 'success' ? 'check-circle' : 'times-circle';
            this.modalIcon.innerHTML = `<i class="fas fa-${icon}"></i>`;
            this.modalIcon.className = `modal-icon ${type}`;
            this.modalTitle.textContent = title;
            this.modalMessage.innerHTML = message;
            
            this.modalButtons.innerHTML = `
                <button class="modal-btn modal-btn-ok">OK</button>
            `;

            const okBtn = this.modalButtons.querySelector('.modal-btn-ok');

            const handleOk = () => {
                this.hide();
                resolve();
                okBtn.removeEventListener('click', handleOk);
            };

            okBtn.addEventListener('click', handleOk);

            this.show();
        });
    }
}

// Initialize modal
const modal = new CustomModal();
