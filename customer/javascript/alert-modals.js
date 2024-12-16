// Alert Modal Functions
function showOrderTypeAlert() {
    const orderTypeAlertEl = document.getElementById('orderTypeAlert');
    if (!orderTypeAlertEl) return;

    let orderTypeAlert = bootstrap.Modal.getInstance(orderTypeAlertEl);
    if (!orderTypeAlert) {
        orderTypeAlert = new bootstrap.Modal(orderTypeAlertEl);
    }
    orderTypeAlert.show();
}

function showEmptyCartAlert() {
    const emptyCartAlertEl = document.getElementById('emptyCartAlert');
    if (!emptyCartAlertEl) return;

    let emptyCartAlert = bootstrap.Modal.getInstance(emptyCartAlertEl);
    if (!emptyCartAlert) {
        emptyCartAlert = new bootstrap.Modal(emptyCartAlertEl);
    }
    emptyCartAlert.show();
}

// Initialize modals when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize empty cart alert modal
    const emptyCartAlert = document.getElementById('emptyCartAlert');
    if (emptyCartAlert) {
        new bootstrap.Modal(emptyCartAlert, {
            backdrop: 'static',
            keyboard: false
        });
    }

    // Initialize order type alert modal
    const orderTypeAlert = document.getElementById('orderTypeAlert');
    if (orderTypeAlert) {
        new bootstrap.Modal(orderTypeAlert, {
            backdrop: 'static',
            keyboard: false
        });
    }

    // Override proceedToCheckout function
    window.proceedToCheckout = function() {
        if (!window.orderCount || window.orderCount === 0) {
            showEmptyCartAlert();
            return;
        }
        
        // Here you would typically redirect to checkout page
        console.log('Order Items:', window.orderItems);
        window.orderItems.forEach(item => {
            console.log(`${item.quantity}x ${item.name} (${item.size}) - ${item.orderType}`);
        });
    };
});
