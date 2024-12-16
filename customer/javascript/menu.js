// Global variables
let selectedSize = 'Uno';
let selectedOrderType = null;
let currentQuantity = 1;
let orderItems = [];
let orderCount = 0;
let currentItemId = null;

// Function to show item details in modal (alias for showDetails for backward compatibility)
const showDetails = showItemDetails;

// Function to show item details in modal
async function showItemDetails(name, price, description, image, isOutOfStock, itemId) {
    // Store current item ID
    currentItemId = itemId;
    
    // Reset selections
    selectedSize = 'Uno';
    selectedOrderType = null;
    currentQuantity = 1;
    
    // Store current item details for adding to cart
    window.currentItem = {
        id: itemId,
        name: name,
        price: price,
        description: description,
        image: image
    };
    
    // Update modal content
    const modal = document.getElementById('itemModal');
    modal.setAttribute('data-item-id', itemId);
    
    document.getElementById('item-name').textContent = name;
    document.getElementById('item-price').textContent = `₱${price.toFixed(2)}`;
    document.getElementById('item-description').textContent = description;
    document.getElementById('quantity').textContent = currentQuantity;
    document.getElementById('item-image').src = image;
    
    // Get size and temperature information
    try {
        const response = await fetch(`get_size_temperature.php?itemId=${itemId}`);
        const data = await response.json();
        
        if (data.success) {
            // Get the size buttons container
            const sizeGroup = document.querySelector('.options-group:first-of-type');
            
            // Clear existing buttons
            sizeGroup.innerHTML = '';
            
            // Create buttons with temperature badges
            data.sizes.forEach(sizeInfo => {
                const button = document.createElement('button');
                button.className = 'option-btn';
                if (sizeInfo.size === 'Uno') {
                    button.classList.add('active');
                }
                button.onclick = () => selectSize(sizeInfo.size, button);
                
                // Add size text
                button.textContent = sizeInfo.size;
                
                // Display temperature badge immediately
                const badge = document.createElement('span');
                badge.className = `temp-badge ${sizeInfo.temperature === 'HOT' ? 'hot' : 'cold'}`;
                badge.textContent = sizeInfo.temperature;
                button.appendChild(badge);
                
                sizeGroup.appendChild(button);
            });
            
            // Update price for initial size
            updatePriceForSize('Uno');
        }
    } catch (error) {
        console.error('Error fetching size information:', error);
    }

    // Reset order type buttons
    document.querySelectorAll('.order-type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Disable add to cart if out of stock
    const confirmBtn = document.querySelector('.btn-confirm');
    if (isOutOfStock) {
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.5';
        confirmBtn.style.cursor = 'not-allowed';
    } else {
        confirmBtn.disabled = false;
        confirmBtn.style.opacity = '1';
        confirmBtn.style.cursor = 'pointer';
    }
}



// Function to add to cart
function addToCart() {
    if (!selectedOrderType) {
        // Show order type required alert using the alert modal
        const orderTypeAlertEl = document.getElementById('orderTypeAlert');
        if (orderTypeAlertEl) {
            const modal = bootstrap.Modal.getInstance(orderTypeAlertEl) || new bootstrap.Modal(orderTypeAlertEl, {
                backdrop: true,
                keyboard: false
            });
            modal.show();
        }
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
    
    // Close the item modal
    const itemModal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
    if (itemModal) {
        itemModal.hide();
    }

    // Show success message
    showToast(`Added ${currentQuantity} ${orderItem.name} to cart`);
} //end bracket of add to cart function

// Function to adjust quantity
function adjustQuantity(change) {
    currentQuantity = Math.max(1, currentQuantity + change);
    document.getElementById('quantity').textContent = currentQuantity;
}

// Function to select size
function selectSize(size, button) {
    selectedSize = size;
    
    // Remove active class from all size buttons
    button.closest('.options-group').querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    button.classList.add('active');
    
    // Update price based on selected size
    updatePriceForSize(size);
}

// Function to update price based on selected size
function updatePriceForSize(size) {
    if (!currentItemId) return;

    const formData = new FormData();
    formData.append('itemId', currentItemId);
    formData.append('size', size);

    fetch('get_item_price.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update price display
            document.getElementById('item-price').textContent = `₱${parseFloat(data.price).toFixed(2)}`;
            
            // Update current item price
            if (window.currentItem) {
                window.currentItem.price = parseFloat(data.price);
            }

            // Handle out of stock status
            const confirmBtn = document.querySelector('.btn-confirm');
            if (data.stock <= 0) {
                confirmBtn.disabled = true;
                confirmBtn.style.opacity = '0.5';
                confirmBtn.style.cursor = 'not-allowed';
                showToast('Selected size is out of stock');
            } else {
                confirmBtn.disabled = false;
                confirmBtn.style.opacity = '1';
                confirmBtn.style.cursor = 'pointer';
            }
        } else {
            console.error('Failed to get price:', data.message);
            showToast('Failed to update price');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating price');
    });
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

    // Remove toast after animation
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Function to proceed to checkout
function proceedToCheckout() {
    if (!orderCount || orderCount === 0) {
        const emptyCartAlertEl = document.getElementById('emptyCartAlert');
        if (!emptyCartAlertEl) {
            console.error('Empty cart alert modal not found');
            return;
        }

        const emptyCartAlert = new bootstrap.Modal(emptyCartAlertEl);
        emptyCartAlert.show();
        return;
    }
    
    // Here you would typically redirect to checkout page
    console.log('Order Items:', orderItems);
    orderItems.forEach(item => {
        console.log(`${item.quantity}x ${item.name} (${item.size}) - ${item.orderType}`);
    });
}



// Function to select order type
function selectOrderType(type, button) {
    selectedOrderType = type;
    
    // Remove active class from all type buttons
    document.querySelectorAll('.options-group:last-of-type .option-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    button.classList.add('active');
}

// Function to update price based on selected size
function updatePriceForSize(size) {
    if (!currentItemId) return;

    const formData = new FormData();
    formData.append('itemId', currentItemId);
    formData.append('size', size);

    fetch('get_item_price.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update price display
            document.getElementById('item-price').textContent = `₱${parseFloat(data.price).toFixed(2)}`;
            
            // Update current item price
            if (window.currentItem) {
                window.currentItem.price = parseFloat(data.price);
            }

            // Handle out of stock status
            const confirmBtn = document.querySelector('.btn-confirm');
            if (data.stock <= 0) {
                confirmBtn.disabled = true;
                confirmBtn.style.opacity = '0.5';
                confirmBtn.style.cursor = 'not-allowed';
                showToast('Selected size is out of stock');
            } else {
                confirmBtn.disabled = false;
                confirmBtn.style.opacity = '1';
                confirmBtn.style.cursor = 'pointer';
            }
        } else {
            console.error('Failed to get price:', data.message);
            showToast('Failed to update price');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating price');
    });
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

    // Remove toast after animation
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Function to proceed to checkout
function proceedToCheckout() {
    console.log('Current order count:', orderCount); // Debug log
    if (!orderCount || orderCount === 0) {
        const emptyCartAlertEl = document.getElementById('emptyCartAlert');
        if (!emptyCartAlertEl) {
            console.error('Empty cart alert modal not found');
            return;
        }

        const emptyCartAlert = new bootstrap.Modal(emptyCartAlertEl);
        emptyCartAlert.show();
        return;
    }
    
    // Here you would typically redirect to checkout page
    console.log('Order Items:', orderItems);
    orderItems.forEach(item => {
        console.log(`${item.quantity}x ${item.name} (${item.size}) - ${item.orderType}`);
    });
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
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

    // Initialize category click handlers
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.getAttribute('data-category');
            
            // Update active state
            document.querySelectorAll('.category-item').forEach(i => {
                i.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update section title
            const sectionTitle = document.querySelector('.section-title');
            sectionTitle.textContent = category === 'all' ? 'ALL ITEMS' : category.toUpperCase();
            
            // Filter menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                const itemCategory = item.querySelector('.item-category').textContent;
                const itemContainer = item.closest('.col');
                if (category === 'all' || itemCategory === category) {
                    itemContainer.style.display = '';
                } else {
                    itemContainer.style.display = 'none';
                }
            });
        });
    });
});
