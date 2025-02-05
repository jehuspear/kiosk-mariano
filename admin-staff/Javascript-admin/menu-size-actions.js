// Menu Size Action Confirmation Handler
class MenuSizeActionHandler {
    constructor() {
        this.modal = null;
        this.initializeModal();
        this.bindEvents();
        this.initializeTemperatureStyles();
    }

    initializeModal() {
        // Create modal HTML
        const modalHTML = `
            <div class="modal fade" id="menuSizeActionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="modal-message"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn confirm-action">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>`;

        // Add modal to document
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = new bootstrap.Modal(document.getElementById('menuSizeActionModal'));
    }

    initializeTemperatureStyles() {
        // Apply initial temperature styles
        document.querySelectorAll('.temperature-type-select').forEach(select => {
            this.updateTemperatureStyle(select);
            
            // Add change event listener
            select.addEventListener('change', (e) => {
                this.updateTemperatureStyle(e.target);
            });
        });

        // Also initialize the new size temperature select
        const newTempSelect = document.getElementById('newTemperatureType');
        if (newTempSelect) {
            this.updateTemperatureStyle(newTempSelect);
            newTempSelect.addEventListener('change', (e) => {
                this.updateTemperatureStyle(e.target);
            });
        }
    }

    updateTemperatureStyle(select) {
        // Remove existing temperature classes
        select.classList.remove('hot', 'iced', 'normal');
        
        // Add new temperature class
        const value = select.value.toLowerCase();
        if (value) {
            select.classList.add(value);
        }

        // Update icon if it exists, or create new one
        let icon = select.parentElement.querySelector('.temperature-icon');
        if (!icon) {
            icon = document.createElement('i');
            icon.className = 'fas temperature-icon';
            select.parentElement.insertBefore(icon, select);
        }

        // Update icon class and color based on temperature
        let iconClass = 'fa-thermometer-half';
        let iconColor = '#69db7c';

        switch (value) {
            case 'hot':
                iconClass = 'fa-fire';
                iconColor = '#ff6b6b';
                break;
            case 'iced':
                iconClass = 'fa-snowflake';
                iconColor = '#74c0fc';
                break;
            case 'normal':
            default:
                iconClass = 'fa-thermometer-half';
                iconColor = '#69db7c';
                break;
        }

        icon.className = `fas ${iconClass} temperature-icon`;
        icon.style.color = iconColor;

        // Update select styling
        select.style.borderColor = iconColor;
        select.style.color = iconColor;
        select.style.backgroundColor = `${iconColor}10`;
    }

    bindEvents() {
        document.addEventListener('DOMContentLoaded', () => {
            // Bind update buttons
            document.querySelectorAll('.update-size-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const sizeId = e.currentTarget.dataset.sizeId;
                    const sizeName = document.getElementById(`size_name_${sizeId}`).value;
                    this.showConfirmation(
                        `Update Size: ${sizeName}`,
                        'Are you sure you want to save these changes?',
                        () => this.handleUpdate(sizeId),
                        'save'
                    );
                });
            });

            // Bind delete buttons
            document.querySelectorAll('.delete-size-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const sizeId = e.currentTarget.dataset.sizeId;
                    const sizeName = document.getElementById(`size_name_${sizeId}`).value;
                    this.showConfirmation(
                        `Delete Size: ${sizeName}`,
                        'Are you sure you want to delete this size? This action cannot be undone.',
                        () => this.handleDelete(sizeId),
                        'delete'
                    );
                });
            });

            // Initialize temperature styles
            this.initializeTemperatureStyles();
        });
    }

    showConfirmation(title, message, callback, type = 'save') {
        const modalElement = document.getElementById('menuSizeActionModal');
        const modalContent = modalElement.querySelector('.modal-content');
        const modalHeader = modalElement.querySelector('.modal-header');
        const modalTitle = modalElement.querySelector('.modal-title');
        const confirmButton = modalElement.querySelector('.confirm-action');
        
        // Reset previous styles
        modalContent.style.border = 'none';
        modalHeader.style.backgroundColor = '';
        modalHeader.style.color = '';
        confirmButton.className = 'btn confirm-action';

        // Apply styles based on type
        if (type === 'delete') {
            modalContent.style.border = '2px solid #dc3545';
            modalHeader.style.backgroundColor = '#dc3545';
            modalHeader.style.color = '#fff';
            confirmButton.className = 'btn btn-danger confirm-action';
            modalTitle.innerHTML = `<i class="fas fa-trash-alt me-2"></i>${title}`;
        } else {
            modalContent.style.border = '2px solid #198754';
            modalHeader.style.backgroundColor = '#198754';
            modalHeader.style.color = '#fff';
            confirmButton.className = 'btn btn-success confirm-action';
            modalTitle.innerHTML = `<i class="fas fa-save me-2"></i>${title}`;
        }

        const modalMessage = modalElement.querySelector('.modal-message');
        modalMessage.textContent = message;
        modalMessage.style.color = '#000'; // Set message text color to black

        // Remove existing event listener and add new one
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

    showAddSizeForm() {
        const addSizeForm = document.getElementById('addSizeForm');
        if (addSizeForm) {
            // Reset form fields
            document.getElementById('newSizeName').value = '';
            document.getElementById('newSizePrice').value = '';
            document.getElementById('newTemperatureType').value = '';
            document.getElementById('newSizeStock').value = '';

            // Reset temperature styling
            const tempSelect = document.getElementById('newTemperatureType');
            this.updateTemperatureStyle(tempSelect);

            // Show form with animation
            addSizeForm.style.display = 'block';
            addSizeForm.style.opacity = '0';
            setTimeout(() => {
                addSizeForm.style.opacity = '1';
            }, 10);
        }
    }

    hideAddSizeForm() {
        const addSizeForm = document.getElementById('addSizeForm');
        if (addSizeForm) {
            // Hide form with animation
            addSizeForm.style.opacity = '0';
            setTimeout(() => {
                addSizeForm.style.display = 'none';
            }, 300);
        }
    }

    createMenuItemSize(menuItemId) {
        const sizeName = document.getElementById('newSizeName').value;
        const price = document.getElementById('newSizePrice').value;
        const temperatureType = document.getElementById('newTemperatureType').value;
        const stock = document.getElementById('newSizeStock').value || 0;

        // Validate inputs
        if (!sizeName) {
            this.showToast('Size name is required', 'error');
            return;
        }
        if (!price) {
            this.showToast('Price is required', 'error');
            return;
        }
        if (!temperatureType) {
            this.showToast('Temperature type is required', 'error');
            return;
        }

        this.showConfirmation(
            `Add New Size: ${sizeName}`,
            'Are you sure you want to add this new size?',
            () => {
                fetch('create_menu_item_size.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `menu_item_id=${menuItemId}&size_name=${sizeName}&price=${price}&temperature_type=${temperatureType}&stock=${stock}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showToast('Size added successfully', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.showToast('Failed to add size: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showToast('Failed to add size', 'error');
                });
            },
            'save'
        );
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
