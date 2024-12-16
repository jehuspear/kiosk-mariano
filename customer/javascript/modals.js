// Modal Alert System
class ModalAlert {
    constructor() {
        this.orderTypeModal = new bootstrap.Modal(document.getElementById('orderTypeAlert'));
        this.emptyCartModal = new bootstrap.Modal(document.getElementById('emptyCartAlert'));
        this.initializeModals();
    }

    initializeModals() {
        // Add alert-modal class to all alert modals
        const alertModals = document.querySelectorAll('#orderTypeAlert, #emptyCartAlert');
        alertModals.forEach(modal => {
            modal.classList.add('alert-modal');
        });

        // Add event listeners for modal close buttons
        document.querySelectorAll('.alert-modal .btn-primary').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = bootstrap.Modal.getInstance(btn.closest('.modal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
    }

    showOrderTypeAlert() {
        this.orderTypeModal.show();
    }

    showEmptyCartAlert() {
        this.emptyCartModal.show();
    }
}

// Initialize Modal Alert System
let modalAlert;
document.addEventListener('DOMContentLoaded', () => {
    modalAlert = new ModalAlert();
    
    // Update the addToCart function to use modal alert
    window.addToCart = function() {
        if (!selectedOrderType) {
            modalAlert.showOrderTypeAlert();
            return;
        }
        
        // Create order item object
        const orderItem = {
            ...window.currentItem,
            size: selectedSize,
            orderType: selectedOrderType,
            quantity: currentQuantity,
            orderId: Date.now()
        };
        
        // Add to order items array
        orderItems.push(orderItem);
        
        // Update order count
        orderCount = orderItems.reduce((total, item) => total + item.quantity, 0);
        document.getElementById('order-count').textContent = orderCount;
        
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
        modal.hide();

        // Show success message
        showToast(`Added ${currentQuantity} ${orderItem.name} to cart`);
    };

    // Update the proceedToCheckout function to use modal alert
    window.proceedToCheckout = function() {
        if (orderCount === 0) {
            modalAlert.showEmptyCartAlert();
            return;
        }
        
        // Here you would typically redirect to checkout page with the order items
        console.log('Order Items:', orderItems);
        orderItems.forEach(item => {
            console.log(`${item.quantity}x ${item.name} (${item.size}) - ${item.orderType}`);
        });
    };
});
