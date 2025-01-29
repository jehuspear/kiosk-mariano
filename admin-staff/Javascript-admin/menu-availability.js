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
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('availabilityModal'));
            modal.hide();
            
            // Show success message
            alert('Menu item availability updated successfully');
        } else {
            throw new Error(data.message || 'Failed to update availability');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update menu item availability');
    });
}
