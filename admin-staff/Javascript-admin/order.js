let clickedButton = null; // Store the reference to the clicked button

// Handle "Add" button clicks
document.querySelectorAll('.order .add-button').forEach(button => {
    button.addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('discountModal'));
        clickedButton = this; // Set clickedButton to the current button

        // Show the discount modal
        modal.show();
    });
});

// Handle "Done" button clicks
document.querySelectorAll('.done-button').forEach(button => {
    button.addEventListener('click', function () {
        const orderId = this.getAttribute('data-order-id');
        console.log('Order ID:', orderId); // Optional: Log the order ID
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();

        // Add event listeners for closing the modal (Yes/No)
        const confirmationButtons = document.querySelectorAll('#confirmationModal .btn-confirm');
        confirmationButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Hide the confirmation modal
                const modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide(); // Close the modal
            });
        });
    });
});

// Show custom discount input
document.getElementById('customDiscountButton').addEventListener('click', (event) => {
    event.preventDefault(); // Prevent default behavior
    document.getElementById('discount-buttons').style.display = 'none';
    document.getElementById('customDiscountInput').style.display = 'block';
});

// Handle custom discount apply
document.getElementById('applyCustomDiscount').addEventListener('click', () => {
    const customValue = document.getElementById('customDiscountValue').value;
    if (customValue && clickedButton) {
        clickedButton.textContent = `${customValue}%`; // Update the clicked button's text
        // Remove the disable logic so it remains clickable
        const modal = bootstrap.Modal.getInstance(document.getElementById('discountModal'));
        modal.hide();
    }
});

// Handle predefined discount button clicks
document.querySelectorAll('#discount-buttons button').forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default behavior
        const discount = this.getAttribute('data-discount');
        if (discount && clickedButton) {
            clickedButton.textContent = `${discount}%`; // Update the clicked button's text
            // Remove the disable logic so it remains clickable
            const modal = bootstrap.Modal.getInstance(document.getElementById('discountModal'));
            modal.hide();
        }
    });
});

// Handle "Back" button to return to predefined discount options
document.getElementById('backToOptions')?.addEventListener('click', (event) => {
    event.preventDefault(); // Prevent default behavior
    document.getElementById('customDiscountInput').style.display = 'none';
    document.getElementById('discount-buttons').style.display = 'block';
});

// Remove the modal backdrop once the modal is fully hidden
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', () => {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove(); // Remove the backdrop manually
        }
    });
});
