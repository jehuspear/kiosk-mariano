// Menu Size Action Confirmation Handler
class MenuSizeActionHandler {
    constructor() {
        this.modal = null;
        this.initializeModal();
        this.bindEvents();
    }

    initializeModal() {
        // Create modal HTML
        const modalHTML = `
            <div class="modal fade" id="menuSizeActionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="modal-message"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary confirm-action">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>`;

        // Add modal to document
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = new bootstrap.Modal(document.getElementById('menuSizeActionModal'));
    }

    bindEvents() {
        document.addEventListener('DOMContentLoaded', () => {
            // Bind update buttons
            document.querySelectorAll('.update-size-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const sizeId = e.currentTarget.dataset.sizeId;
                    this.showConfirmation(
                        'Update Size',
                        'Are you sure you want to save these changes?',
                        () => this.handleUpdate(sizeId)
                    );
                });
            });

            // Bind delete buttons
            document.querySelectorAll('.delete-size-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const sizeId = e.currentTarget.dataset.sizeId;
                    this.showConfirmation(
                        'Delete Size',
                        'Are you sure you want to delete this size? This action cannot be undone.',
                        () => this.handleDelete(sizeId)
                    );
                });
            });
        });
    }

    showConfirmation(title, message, callback) {
        const modalElement = document.getElementById('menuSizeActionModal');
        modalElement.querySelector('.modal-title').textContent = title;
        modalElement.querySelector('.modal-message').textContent = message;

        // Remove existing event listener and add new one
        const confirmButton = modalElement.querySelector('.confirm-action');
        const newConfirmButton = confirmButton.cloneNode(true);
        confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

        newConfirmButton.addEventListener('click', () => {
            this.modal.hide();
            callback();
        });

        this.modal.show();
    }

    handleUpdate(sizeId) {
        const sizeName = document.getElementById(`size_name_${sizeId}`).value;
        const price = document.getElementById(`price_${sizeId}`).value;
        const temperatureType = document.getElementById(`temperature_type_${sizeId}`).value;
        const stock = document.getElementById(`stock_${sizeId}`).value || 0; // Allow zero stock

        fetch('update_menu_item_size.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `size_id=${sizeId}&size_name=${sizeName}&price=${price}&temperature_type=${temperatureType}&stock=${stock}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showToast('Size updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showToast('Failed to update size: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showToast('Failed to update size', 'error');
        });
    }

    handleDelete(sizeId) {
        fetch('delete_menu_item_size.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `size_id=${sizeId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showToast('Size deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showToast('Failed to delete size: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showToast('Failed to delete size', 'error');
        });
    }

    showToast(message, type = 'info') {
        const toastHTML = `
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>`;

        // Remove existing toast container if any
        const existingContainer = document.querySelector('.toast-container');
        if (existingContainer) {
            existingContainer.remove();
        }

        // Add new toast
        document.body.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.querySelector('.toast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
}

// Initialize the handler
const menuSizeActionHandler = new MenuSizeActionHandler();
