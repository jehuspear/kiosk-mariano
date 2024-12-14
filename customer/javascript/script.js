// Global variables to track order state
let orderCount = 0;
let selectedSize = '22oz';
let selectedOrderType = null;
let currentQuantity = 1;
let orderItems = [];

// Show Item Details Modal
function showDetails(name, price, description) {
    // Reset selections
    selectedSize = '22oz';
    selectedOrderType = null;
    currentQuantity = 1;
    
    // Update modal content
    document.getElementById('item-name').textContent = name;
    document.getElementById('item-price').textContent = `₱${price}.00`;
    document.getElementById('item-description').textContent = description;
    document.getElementById('quantity').textContent = currentQuantity;
    
    // Set the image source based on the item name
    const imageName = name.toLowerCase().replace(/ /g, '-');
    document.getElementById('item-image').src = `resources/menu-items/${imageName}.jpg`;
    
    // Reset size buttons
    document.querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent === '22oz') {
            btn.classList.add('active');
        }
    });

    // Reset order type buttons
    document.querySelectorAll('.options-group:last-child .option-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Store current item details for adding to cart
    window.currentItem = {
        name: name,
        price: price,
        description: description
    };
}

// Handle quantity adjustment
function adjustQuantity(change) {
    currentQuantity = Math.max(1, currentQuantity + change);
    document.getElementById('quantity').textContent = currentQuantity;
}

// Handle size selection
function selectSize(size, button) {
    selectedSize = size;
    // Remove active class from all size buttons in the same options group
    button.closest('.options-group').querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    // Add active class to clicked button
    button.classList.add('active');
}

// Handle order type selection
function selectOrderType(type, button) {
    selectedOrderType = type;
    // Remove active class from all type buttons in the same options group
    button.closest('.options-group').querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    // Add active class to clicked button
    button.classList.add('active');
}

// Add to cart functionality
function addToCart() {
    if (!selectedOrderType) {
        alert('Please select an order type (Dine In or Take Out)');
        return;
    }
    
    // Create order item object
    const orderItem = {
        ...window.currentItem,
        size: selectedSize,
        orderType: selectedOrderType,
        quantity: currentQuantity,
        id: Date.now() // unique identifier
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
}

// Cancel order
function cancelOrder() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
    modal.hide();
}

// Show toast message
function showToast(message) {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast-message');
    if (existingToast) {
        existingToast.remove();
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.textContent = message;
    document.body.appendChild(toast);

    // Add toast styles dynamically
    const style = document.createElement('style');
    style.textContent = `
        .toast-message {
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            z-index: 1000;
            animation: fadeInOut 3s ease;
        }
        
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translate(-50%, 20px); }
            10% { opacity: 1; transform: translate(-50%, 0); }
            90% { opacity: 1; transform: translate(-50%, 0); }
            100% { opacity: 0; transform: translate(-50%, -20px); }
        }
    `;
    document.head.appendChild(style);

    // Remove toast after animation
    setTimeout(() => {
        toast.remove();
        style.remove();
    }, 3000);
}

// Proceed to Checkout
function proceedToCheckout() {
    if (orderCount === 0) {
        alert('Please add items to your cart before proceeding to checkout');
        return;
    }
    
    // Here you would typically redirect to checkout page with the order items
    console.log('Order Items:', orderItems);
    // For now, just show the items in console
    orderItems.forEach(item => {
        console.log(`${item.quantity}x ${item.name} (${item.size}) - ${item.orderType}`);
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                const itemName = item.querySelector('.item-name').textContent.toLowerCase();
                const itemContainer = item.closest('.col');
                if (itemName.includes(searchTerm)) {
                    itemContainer.style.display = '';
                } else {
                    itemContainer.style.display = 'none';
                }
            });
        });
    }
});
