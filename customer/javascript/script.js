// Global variables to track order state
let orderCount = 0;
let selectedSize = '22oz';
let selectedOrderType = null;
let itemModal = null;

// Initialize Bootstrap modal when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
});

// Show Item Details Modal
function showDetails(name, price, description) {
    // Update modal content
    document.getElementById('item-name').textContent = name;
    document.getElementById('item-price').textContent = `₱${price}.00`;
    document.getElementById('item-description').textContent = description;
    
    // Set the image source based on the item name
    const imageName = name.toLowerCase().replace(/ /g, '-');
    document.getElementById('item-image').src = `resources/menu-items/${imageName}.jpg`;
    
    // Reset selections
    selectedSize = '22oz';
    selectedOrderType = null;
    resetSelections();
}

// Reset size and type selections
function resetSelections() {
    // Reset size buttons
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent === '22oz') {
            btn.classList.add('active');
        }
    });

    // Reset type buttons
    document.querySelectorAll('.type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
}

// Handle size selection
function selectSize(size, button) {
    selectedSize = size;
    // Remove active class from all size buttons
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    // Add active class to clicked button
    button.classList.add('active');
}

// Handle order type selection
function selectOrderType(type, button) {
    selectedOrderType = type;
    // Remove active class from all type buttons
    document.querySelectorAll('.type-btn').forEach(btn => {
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
    
    orderCount++;
    document.getElementById('order-count').textContent = orderCount;
    
    // Close the modal using Bootstrap's modal API
    if (itemModal) {
        itemModal.hide();
    }
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                const itemName = item.querySelector('.item-name').textContent.toLowerCase();
                const itemContainer = item.closest('.col-md-6');
                if (itemName.includes(searchTerm)) {
                    itemContainer.style.display = 'block';
                } else {
                    itemContainer.style.display = 'none';
                }
            });
        });
    }
});
