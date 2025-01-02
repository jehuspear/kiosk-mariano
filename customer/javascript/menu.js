// Global variables
let selectedSize = null;
let selectedOrderType = null;
let currentQuantity = 1;
let currentItemId = null;

// Function to show item details in modal (alias for showDetails for backward compatibility)
const showDetails = showItemDetails;

// Function to show item details in modal
async function showItemDetails(name, price, description, image, isOutOfStock, itemId) {
    // Store current item ID
    currentItemId = itemId;
    
    // Reset selections
    selectedSize = null;
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
    document.getElementById('item-description').textContent = description;
    document.getElementById('quantity').textContent = currentQuantity;
    document.getElementById('item-image').src = image;
    
    // Get size and temperature information
    try {
        console.log('Fetching sizes for item ID:', itemId);
        const response = await fetch(`get_size_temperature.php?itemId=${itemId}`);
        const text = await response.text();
        console.log('Raw response:', text);
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            throw new Error('Invalid JSON response');
        }
        
        if (data.success && data.sizes.length > 0) {
            console.log('Received sizes:', data.sizes);
            
            // Get the size buttons container
            const sizeGroup = document.querySelector('.options-group');
            
            // Clear existing buttons
            sizeGroup.innerHTML = '';
            
            // Create buttons with temperature badges
            data.sizes.forEach((sizeInfo, index) => {
                // Create size option container
                const sizeOption = document.createElement('div');
                sizeOption.className = 'size-option';
                
                // Create button
                const button = document.createElement('button');
                button.className = 'option-btn';
                
                // Make first (lowest price) button active by default
                if (index === 0) {
                    button.classList.add('active');
                    selectedSize = sizeInfo.size;
                    window.currentItem.price = parseFloat(sizeInfo.price);
                    document.getElementById('item-price').textContent = `₱${parseFloat(sizeInfo.price).toFixed(2)}`;
                }
                
                button.onclick = () => selectSize(sizeInfo.size, button, sizeInfo.price, sizeInfo.stock);
                
                // Add size text (use the size name directly from database)
                button.textContent = sizeInfo.size;
                
                // Create temperature badge with icon
                const badge = document.createElement('span');
                badge.className = `temp-badge ${sizeInfo.temperature.toLowerCase()}`;
                
                // Add icon based on temperature
                let icon = '';
                switch(sizeInfo.temperature) {
                    case 'HOT':
                        icon = '<i class="fas fa-fire"></i>';
                        break;
                    case 'ICED':
                        icon = '<i class="fas fa-snowflake"></i>';
                        break;
                    case 'NORMAL':
                        icon = '<i class="fas fa-thermometer-half"></i>';
                        break;
                }
                
                badge.innerHTML = `${icon} ${sizeInfo.temperature}`;
                
                // Add badge to button
                button.appendChild(badge);
                
                // Add stock info
                if (sizeInfo.stock <= 0) {
                    const stockBadge = document.createElement('span');
                    stockBadge.className = 'stock-badge out-of-stock';
                    stockBadge.innerHTML = '<i class="fas fa-times-circle"></i> Out of Stock';
                    button.appendChild(stockBadge);
                    button.disabled = true;
                }
                
                // Add button to size option container
                sizeOption.appendChild(button);
                
                // Add size option to group
                sizeGroup.appendChild(sizeOption);
            });
        } else {
            console.error('Server returned error:', data.message);
            showToast(data.message || 'Failed to load size information');
        }
    } catch (error) {
        console.error('Error fetching size information:', error);
        showToast('Error loading size information. Please try again.');
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
async function addToCart() {
    console.log('Adding to cart...');
    console.log('Selected order type:', selectedOrderType);
    console.log('Current item:', window.currentItem);
    console.log('Selected size:', selectedSize);
    console.log('Current quantity:', currentQuantity);

    if (!selectedOrderType) {
        console.log('No order type selected');
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
        quantity: currentQuantity
    };
    console.log('Order item to be sent:', orderItem);
    
    try {
        // Send to PHP endpoint
        console.log('Sending request to add_to_cart.php...');
        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderItem)
        });

        console.log('Response received:', response);
        const text = await response.text();
        console.log('Raw response text:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Failed to parse JSON response:', e);
            throw new Error('Invalid JSON response from server');
        }
        
        console.log('Parsed response data:', data);
        
        if (data.success) {
            // Update order count display
            document.getElementById('order-count').textContent = data.cartCount;
            console.log('Cart count updated to:', data.cartCount);
            
            // Close the item modal
            const itemModal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
            if (itemModal) {
                itemModal.hide();
            }

            // Show success message
            showToast(`Added ${currentQuantity} ${orderItem.name} to cart`);
        } else {
            console.error('Server returned error:', data.message);
            showToast(data.message || 'Failed to add item to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Error adding item to cart');
    }
}

// Function to adjust quantity
function adjustQuantity(change) {
    currentQuantity = Math.max(1, currentQuantity + change);
    document.getElementById('quantity').textContent = currentQuantity;
}

// Function to select size
function selectSize(size, button, price, stock) {
    if (stock <= 0) {
        showToast('This size is out of stock');
        return;
    }

    selectedSize = size;
    
    // Remove active class from all size buttons
    button.closest('.options-group').querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    button.classList.add('active');
    
    // Update price display
    document.getElementById('item-price').textContent = `₱${parseFloat(price).toFixed(2)}`;
    window.currentItem.price = parseFloat(price);
}

// Function to select order type
function selectOrderType(type, button) {
    selectedOrderType = type;

    // Get all order type buttons
    const buttons = document.querySelectorAll('.order-type-group .order-type-btn');
    
    // Loop through the buttons and deactivate them
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Activate the clicked button
    button.classList.add('active');
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
// function proceedToCheckout() {
//     // Get the cart count from the span element
//     const orderCountText = document.getElementById('order-count').textContent;
//     const orderCount = parseInt(orderCountText);
//     console.log('Order count text:', orderCountText); // Debug log
//     console.log('Parsed order count:', orderCount); // Debug log
    
//     // Check if we have items in cart
//     if (!isNaN(orderCount) && orderCount > 0) {
//         window.location.href = 'checkout-list.php';
//     } else {
//         const emptyCartAlertEl = document.getElementById('emptyCartAlert');
//         if (!emptyCartAlertEl) {
//             console.error('Empty cart alert modal not found');
//             return;
//         }

//         const emptyCartAlert = new bootstrap.Modal(emptyCartAlertEl);
//         emptyCartAlert.show();
//     }
// } //End of proceedToCheckout Function

// Function to select order type
function selectOrderType(type, button) {
    selectedOrderType = type;

    // Get all order type buttons
    const buttons = document.querySelectorAll('.order-type-group .order-type-btn');
    
    // Loop through the buttons and deactivate them
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Activate the clicked button
    button.classList.add('active');
}


// THE SEARCH FUNCTIONALITY --Start--
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
    }); //END of Search Functionality

    // Update initial cart count from session
    fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('order-count').textContent = data.count;
            }
        })
        .catch(console.error);
});

// Function to proceed to checkout
function proceedToCheckout() {
    console.log('Proceeding to checkout...');
    
    // Get the current cart count from the server
    fetch('get_cart_count.php')
        .then(response => {
            console.log('Cart count response:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Cart count data:', data);
            if (data.success) {
                const orderCount = data.count;
                console.log('Cart count from server:', orderCount);
                
                if (orderCount > 0) {
                    console.log('Redirecting to checkout page...');
                    window.location.href = 'checkout-list.php';
                } else {
                    console.log('Showing empty cart alert...');
                    const emptyCartAlertEl = document.getElementById('emptyCartAlert');
                    if (!emptyCartAlertEl) {
                        console.error('Empty cart alert modal not found');
                        showToast('Error: Cart is empty');
                        return;
                    }

                    const emptyCartAlert = new bootstrap.Modal(emptyCartAlertEl, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    emptyCartAlert.show();
                }
            } else {
                console.error('Failed to get cart count:', data);
                showToast('Error checking cart status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error checking cart status');
        });
}
