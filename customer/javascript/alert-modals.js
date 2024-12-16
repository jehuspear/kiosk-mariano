// Alert Modal Functions
function showOrderTypeAlert() {
    const orderTypeAlert = new bootstrap.Modal(document.getElementById('orderTypeAlert'));
    orderTypeAlert.show();
}

function showEmptyCartAlert() {
    const emptyCartAlert = new bootstrap.Modal(document.getElementById('emptyCartAlert'));
    emptyCartAlert.show();
}

// Add class to modals for custom styling
document.addEventListener('DOMContentLoaded', function() {
    const alertModals = document.querySelectorAll('#orderTypeAlert, #emptyCartAlert');
    alertModals.forEach(modal => {
        modal.classList.add('alert-modal');
    });
});
