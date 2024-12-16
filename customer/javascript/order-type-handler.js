// Function to handle size selection
function selectSize(size, button) {
    // Remove selected class from all size buttons
    const sizeButtons = document.querySelectorAll('.size-option .option-btn');
    sizeButtons.forEach(btn => btn.classList.remove('selected'));
    
    // Add selected class to clicked button
    button.classList.add('selected');
    
    // Store the selected size
    window.selectedSize = size;
}

// Function to handle order type selection
function selectOrderType(type, button) {
    // Remove selected class from all order type buttons
    const orderTypeButtons = document.querySelectorAll('.order-type-btn');
    orderTypeButtons.forEach(btn => btn.classList.remove('selected'));
    
    // Add selected class to clicked button
    button.classList.add('selected');
    
    // Store the selected order type
    window.selectedOrderType = type;
}

// Function to validate selections before adding to cart
function validateSelections() {
    if (!window.selectedOrderType) {
        // Show the order type alert modal
        const orderTypeAlertEl = document.getElementById('orderTypeAlert');
        if (orderTypeAlertEl) {
            const modal = bootstrap.Modal.getInstance(orderTypeAlertEl) || new bootstrap.Modal(orderTypeAlertEl);
            modal.show();
        }
        return false;
    }
    return true;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Reset selections when item modal is opened
    const itemModal = document.getElementById('itemModal');
    if (itemModal) {
        itemModal.addEventListener('show.bs.modal', function() {
            window.selectedOrderType = null;
            document.querySelectorAll('.order-type-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
        });
    }

    // Initialize order type alert modal
    const orderTypeAlert = document.getElementById('orderTypeAlert');
    if (orderTypeAlert) {
        new bootstrap.Modal(orderTypeAlert, {
            backdrop: true,
            keyboard: false
        });
    }
});
