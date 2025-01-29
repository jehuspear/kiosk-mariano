function toggleAvailability(button, menuItemId) {
    const currentAvailability = button.classList.contains('btn-success');
    const newAvailability = !currentAvailability;
    
    // Show confirmation modal
    const modalHtml = `
        <div class="modal fade" id="availabilityModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Availability Change</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to mark this item as ${newAvailability ? 'Available' : 'Unavailable'}?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn ${newAvailability ? 'btn-success' : 'btn-danger'}" onclick="updateAvailability(${menuItemId}, ${newAvailability})">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove any existing modal
    const existingModal = document.getElementById('availabilityModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('availabilityModal'));
    modal.show();
}

function showSuccessModal(newAvailability) {
    const modalHtml = `
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header ${newAvailability ? 'bg-success' : 'bg-danger'} text-white">
                        <h5 class="modal-title">
                            <i class="fas ${newAvailability ? 'fa-check-circle' : 'fa-times-circle'} me-2"></i>
                            Success
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas ${newAvailability ? 'fa-check-circle' : 'fa-times-circle'} ${newAvailability ? 'text-success' : 'text-danger'}" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">Menu item has been marked as <strong>${newAvailability ? 'Available' : 'Unavailable'}</strong></p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn ${newAvailability ? 'btn-success' : 'btn-danger'}" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove any existing success modal
    const existingModal = document.getElementById('successModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
}

function updateAvailability(menuItemId, newAvailability) {
    // Send AJAX request to update availability
    fetch('update_menu_availability.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `menuItemId=${menuItemId}&availability=${newAvailability}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button appearance
            const button = document.querySelector(`[data-menu-id="${menuItemId}"]`);
            if (newAvailability) {
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
                button.textContent = 'AVAILABLE';
            } else {
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
                button.textContent = 'UNAVAILABLE';
            }
            
            // Close the confirmation modal
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('availabilityModal'));
            confirmModal.hide();
            
            // Show success modal
            showSuccessModal(newAvailability);
        } else {
            throw new Error(data.message || 'Failed to update availability');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal();
    });
}

function showErrorModal() {
    const modalHtml = `
        <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">Failed to update menu item availability</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove any existing error modal
    const existingModal = document.getElementById('errorModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

// Function to check stock and update availability
function checkStockAndUpdateAvailability(menuItemId, stock) {
    fetch('update_stock_availability.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `menuItemId=${menuItemId}&stock=${stock}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button appearance if availability changed
            const button = document.querySelector(`[data-menu-id="${menuItemId}"]`);
            if (data.availability === 'Unavailable') {
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
                button.textContent = 'UNAVAILABLE';
                
                // Show modal for automatic unavailability update
                const modalHtml = `
                    <div class="modal fade" id="autoUnavailableModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Stock Alert
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <i class="fas fa-box-open text-warning" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">This item has been automatically marked as <strong>Unavailable</strong> due to zero stock.</p>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove any existing auto unavailable modal
                const existingModal = document.getElementById('autoUnavailableModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Add new modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('autoUnavailableModal'));
                modal.show();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Add event listener for stock changes
document.addEventListener('DOMContentLoaded', function() {
    // Listen for changes to stock input fields
    document.querySelectorAll('.stock-input').forEach(input => {
        input.addEventListener('change', function() {
            const menuItemId = this.dataset.menuId;
            const stock = parseInt(this.value) || 0;
            checkStockAndUpdateAvailability(menuItemId, stock);
        });
    });
});
