

// Global variables to track order state
let orderCount = 0;
let selectedSize = '22oz';
let selectedOrderType = null;

// Show Item Details Modal
function showDetails(name, price, description) {
    const modal = document.getElementById('item-details-modal');
    const itemImage = document.getElementById('item-image');
    
    modal.style.display = 'flex';
    document.getElementById('item-name').textContent = name;
    document.getElementById('item-price').textContent = `₱${price}.00`;
    document.getElementById('item-description').textContent = description;
    
    // Set the image source based on the item name
    const imageName = name.toLowerCase().replace(' ', '-');
    itemImage.src = `resources/menu-items/${imageName}.jpg`;
    
    // Reset selections
    selectedSize = '22oz';
    selectedOrderType = null;
    updateButtonStates();
}

// Close Item Details Modal
function closeDetails() {
    document.getElementById('item-details-modal').style.display = 'none';
}

// Handle size selection
function selectSize(size, button) {
    selectedSize = size;
    // Remove selected class from all size buttons
    document.querySelectorAll('.size-options button').forEach(btn => {
        btn.classList.remove('selected');
    });
    // Add selected class to clicked button
    button.classList.add('selected');
}

// Handle order type selection
function selectOrderType(type, button) {
    selectedOrderType = type;
    // Remove selected class from all order type buttons
    document.querySelectorAll('.order-type button').forEach(btn => {
        btn.classList.remove('selected');
    });
    // Add selected class to clicked button
    button.classList.add('selected');
}

// Update button states
function updateButtonStates() {
    // Update size buttons
    document.querySelectorAll('.size-options button').forEach(button => {
        const size = button.textContent;
        if (size === selectedSize) {
            button.classList.add('selected');
        } else {
            button.classList.remove('selected');
        }
    });
    
    // Update order type buttons
    if (selectedOrderType) {
        document.querySelectorAll('.order-type button').forEach(button => {
            if (button.textContent.toLowerCase() === selectedOrderType.toLowerCase()) {
                button.classList.add('selected');
            } else {
                button.classList.remove('selected');
            }
        });
    }
}

// Add to cart functionality
function addToCart() {
    if (!selectedOrderType) {
        alert('Please select an order type (Dine In or Take Out)');
        return;
    }
    
    orderCount++;
    document.getElementById('order-count').textContent = orderCount;
    closeDetails();
}

// Proceed to Checkout
function proceedToCheckout() {
    if (orderCount === 0) {
        alert('Please add items to your cart before proceeding to checkout');
        return;
    }
    window.location.href = 'checkout.php';
}

// Search functionality
document.getElementById('search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const itemName = item.querySelector('.item-name').textContent.toLowerCase();
        if (itemName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('item-details-modal');
    if (event.target === modal) {
        closeDetails();
    }
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for size buttons
    document.querySelectorAll('.size-options button').forEach(button => {
        button.addEventListener('click', function() {
            selectSize(this.textContent, this);
        });
    });

    // Add click handlers for order type buttons
    document.querySelectorAll('.order-type button').forEach(button => {
        button.addEventListener('click', function() {
            selectOrderType(this.textContent, this);
        });
    });

    // Add click handler for add to cart button
    document.querySelector('.add-to-cart').addEventListener('click', addToCart);
});
