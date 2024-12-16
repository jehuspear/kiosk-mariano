// Alert Modal Functions
function showOrderTypeAlert() {
    const orderTypeAlertEl = document.getElementById('orderTypeAlert');
    if (!orderTypeAlertEl) {
        console.error('Order type alert modal not found');
        return;
    }

    try {
        const orderTypeAlert = bootstrap.Modal.getInstance(orderTypeAlertEl) || new bootstrap.Modal(orderTypeAlertEl, {
            backdrop: 'static',
            keyboard: false
        });
        orderTypeAlert.show();
    } catch (error) {
        console.error('Error showing order type alert:', error);
    }
}

function showEmptyCartAlert() {
    const emptyCartAlertEl = document.getElementById('emptyCartAlert');
    if (!emptyCartAlertEl) {
        console.error('Empty cart alert modal not found');
        return;
    }

    try {
        const emptyCartAlert = bootstrap.Modal.getInstance(emptyCartAlertEl) || new bootstrap.Modal(emptyCartAlertEl, {
            backdrop: 'static',
            keyboard: false
        });
        emptyCartAlert.show();
    } catch (error) {
        console.error('Error showing empty cart alert:', error);
    }
}

// Initialize modals when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize empty cart alert modal
        const emptyCartAlert = document.getElementById('emptyCartAlert');
        if (emptyCartAlert) {
            new bootstrap.Modal(emptyCartAlert, {
                backdrop: 'static',
                keyboard: false
            });
        } else {
            console.warn('Empty cart alert modal element not found');
        }

        // Initialize order type alert modal
        const orderTypeAlert = document.getElementById('orderTypeAlert');
        if (orderTypeAlert) {
            new bootstrap.Modal(orderTypeAlert, {
                backdrop: 'static',
                keyboard: false
            });
        } else {
            console.warn('Order type alert modal element not found');
        }
    } catch (error) {
        console.error('Error initializing modals:', error);
    }
});

// Export functions for use in other scripts
window.showOrderTypeAlert = showOrderTypeAlert;
window.showEmptyCartAlert = showEmptyCartAlert;
