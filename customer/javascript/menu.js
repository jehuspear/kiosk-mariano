// Global variables to track order state
let selectedSize = 'Sinco';
let selectedOrderType = null;
let currentQuantity = 1;
let orderItems = [];
let orderCount = 0;
let currentItemId = null;
let currentCategory = 'all';

// Function to update menu items based on category
function updateMenuItems(category) {
    // Update active state of category items
    document.querySelectorAll('.category-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-category') === category) {
            item.classList.add('active');
        }
    });

    // Update section title
    const sectionTitle = document.querySelector('.section-title');
    sectionTitle.textContent = category === 'all' ? 'ALL ITEMS' : category.toUpperCase();

    // Add loading state
    const menuContainer = document.querySelector('#menu-items-container');
    menuContainer.classList.add('loading');

    // Fetch menu items for selected category
    fetch(`get_menu_by_category.php?category=${encodeURIComponent(category)}`)
        .then(response => response.json())
        .then(items => {
            menuContainer.innerHTML = ''; // Clear current items

            items.forEach(item => {
                const outOfStock = item.stock <= 0;
                const bestSellerBadge = item.best_seller_rank ? 
                    `<div class="best-seller-badge best-seller-rank-${item.best_seller_rank}">
                        <i class="fas fa-star"></i>
                        Best Seller #${item.best_seller_rank}
                    </div>` : '';

                const modalAttributes = outOfStock ? '' : `data-bs-toggle="modal" data-bs-target="#itemModal" 
                                                        onclick="showDetails('${escapeHtml(item.name)}', 
                                                        ${item.price}, 
                                                        '${escapeHtml(item.description)}',
                                                        '${escapeHtml(item.image)}',
                                                        ${outOfStock},
                                                        ${item.id})"`;
                const itemHtml = `
                    <div class="col">
                        <div class="menu-item ${outOfStock ? 'out-of-stock-item' : ''}" ${modalAttributes}>
                            ${outOfStock ? `
                                <div class="out-of-stock">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Out of Stock
                                </div>
                            ` : ''}
                            ${bestSellerBadge}
                            <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">
                            <div class="item-details">
                                <p class="item-name">${escapeHtml(item.name)}</p>
                                <p class="item-category">${escapeHtml(item.category)}</p>
                                <p class="item-price">₱${parseFloat(item.price).toFixed(2)}</p>
                            </div>
                        </div>
                    </div>
                `;
                menuContainer.insertAdjacentHTML('beforeend', itemHtml);
            });

            // Add CSS for out-of-stock items
            const style = document.createElement('style');
            style.textContent = `
                .out-of-stock-item {
                    opacity: 0.7;
                    cursor: not-allowed !important;
                    pointer-events: none;
                }
                .out-of-stock-item:hover {
                    transform: none !important;
                }
            `;
            document.head.appendChild(style);
        })
        .catch(error => {
            console.error('Error fetching menu items:', error);
            showToast('Error loading menu items');
        })
        .finally(() => {
            menuContainer.classList.remove('loading');
        });
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Function to show item details in modal
function showDetails(name, price, description, image, isOutOfStock, itemId) {
    // Reset selections
    selectedSize = 'Sinco';
    selectedOrderType = null;
    currentQuantity = 1;
    currentItemId = itemId;
    
    // Update modal content
    document.getElementById('item-name').textContent = name;
    document.getElementById('item-price').textContent = `₱${price.toFixed(2)}`;
    document.getElementById('item-description').textContent = description;
    document.getElementById('quantity').textContent = currentQuantity;
    
    // Set the image source
    document.getElementById('item-image').src = image;
    
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
    
    // Reset size buttons
    document.querySelectorAll('.options-group:first-of-type .option-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent === 'Sinco') {
            btn.classList.add('active');
        }
    });

    // Reset order type buttons
    document.querySelectorAll('.options-group:last-of-type .option-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Store current item details for adding to cart
    window.currentItem = {
        id: itemId,
        name: name,
        price: price,
        description: description,
        image: image
    };

    // Update price for initial size
    updatePriceForSize('Sinco');
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
    
    // Update price based on selected size
    updatePriceForSize(size);
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

// Add to cart functionality
function addToCart() {
    if (!selectedOrderType) {
        showOrderTypeAlert();
        return;
    }
    
    // Create order item object
    const orderItem = {
        ...window.currentItem,
        size: selectedSize,
        orderType: selectedOrderType,
        quantity: currentQuantity,
        orderId: Date.now() // unique identifier for the order
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

    // Remove toast after animation
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Proceed to Checkout
function proceedToCheckout() {
    if (orderCount === 0) {
        showEmptyCartAlert();
        return;
    }
    
    // Here you would typically redirect to checkout page with the order items
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
            updateMenuItems(category);
        });
    });

    // Load initial menu items
    updateMenuItems('all');
});
