document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
    const menuItemsContainer = document.getElementById('menuItemsContainer');
    const cartItems = document.getElementById('cartItems');
    const searchInput = document.getElementById('searchInput');
    const subtotalElement = document.getElementById('subtotal');
    const discountElement = document.getElementById('discount');
    const totalElement = document.getElementById('total');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const clearCartBtn = document.getElementById('clearCartBtn');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    // Initialize sidebar state
    const sidebarCollapsed = localStorage.getItem('pos-sidebar-collapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.style.display = 'none';
        mainContent.style.marginLeft = '0';
    }

    // Sidebar toggle handler
    sidebarToggle.addEventListener('click', function() {
        if (sidebar.style.display === 'none') {
            sidebar.style.display = 'block';
            mainContent.style.marginLeft = '250px';
            localStorage.setItem('pos-sidebar-collapsed', 'false');
        } else {
            sidebar.style.display = 'none';
            mainContent.style.marginLeft = '0';
            localStorage.setItem('pos-sidebar-collapsed', 'true');
        }
    });

    // Cart state
    let cart = [];

    // Load menu items on page load
    loadMenuItems();

    // Update datetime
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
            timeZone: 'Asia/Manila'
        };
        const dateTimeStr = now.toLocaleString('en-US', options);
        document.getElementById('datetime').textContent = dateTimeStr;
    }

    // Update datetime every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Event listeners
    const clearSearchBtn = document.getElementById('clearSearch');

    searchInput.addEventListener('input', function(event) {
        handleSearch(event);
        // Show/hide clear button based on input value
        clearSearchBtn.style.display = this.value ? 'block' : 'none';
    });

    // Clear search functionality
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
        this.style.display = 'none';
        // Trigger search to show all items
        handleSearch({ target: searchInput });
    });

    checkoutBtn.addEventListener('click', handleCheckout);
    clearCartBtn.addEventListener('click', clearCart);

    // Category filtering
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Filter items
            const category = btn.dataset.category;
            const menuItems = document.querySelectorAll('.menu-item-card');
            
            menuItems.forEach(item => {
                const itemCategory = item.querySelector('.menu-item-category').textContent;
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Function to filter items by both search and category
    function filterItems() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeCategory = document.querySelector('.category-btn.active').dataset.category;
        const menuItems = document.querySelectorAll('.menu-item-card');

        menuItems.forEach(item => {
            const itemName = item.querySelector('.menu-item-name').textContent.toLowerCase();
            const itemCategory = item.querySelector('.menu-item-category').textContent;
            const matchesSearch = itemName.includes(searchTerm);
            const matchesCategory = activeCategory === 'all' || itemCategory === activeCategory;

            if (matchesSearch && matchesCategory) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Update search handler to use combined filtering
    function handleSearch(event) {
        filterItems();
    }

    // Load menu items from the database
    async function loadMenuItems() {
        try {
            console.log('Fetching menu items...');
            const response = await fetch('/kiosk-mariano/admin-staff/get_menu_items.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            console.log('Menu items data:', data);
            
            if (data.success) {
                if (data.menuItems && data.menuItems.length > 0) {
                    console.log(`Rendering ${data.count} menu items`);
                    renderMenuItems(data.menuItems);
                } else {
                    console.warn('No menu items found');
                    menuItemsContainer.innerHTML = '<div class="alert alert-info">No menu items available.</div>';
                }
            } else {
                console.error('Failed to load menu items:', data.message);
                menuItemsContainer.innerHTML = '<div class="alert alert-danger">Failed to load menu items. Please try again.</div>';
            }
        } catch (error) {
            console.error('Error loading menu items:', error);
            menuItemsContainer.innerHTML = '<div class="alert alert-danger">An error occurred while loading menu items.</div>';
        }
    }

    // Render menu items to the container
    function renderMenuItems(menuItems) {
        console.log('Starting to render menu items');
        const menuItemsHtml = menuItems.map(item => {
            console.log('Rendering item:', item.MenuItem_Name);
            return `
            <div class="menu-item-card" data-item-id="${item.MenuItem_ID}">
                ${item.bestSeller ? `<div class="best-seller-badge">Best Seller #${item.bestSeller}</div>` : ''}
                <img src="${item.MenuItem_Image}" alt="${item.MenuItem_Name}" class="menu-item-image">
                <div class="menu-item-details">
                    <h5 class="menu-item-name">${item.MenuItem_Name}</h5>
                    <div class="menu-item-category">${item.MenuItem_Category}</div>
                    <div class="menu-item-sizes">
                        ${renderSizeOptions(item.sizes)}
                    </div>
                </div>
            </div>
        `}).join('');
        console.log('Finished generating HTML');
        menuItemsContainer.innerHTML = menuItemsHtml;
        console.log('Menu items rendered to container');

        // Add click event listeners to size options
        document.querySelectorAll('.size-option').forEach(option => {
            option.addEventListener('click', handleSizeSelection);
        });
    }

    // Get temperature icon
    function getTemperatureIcon(temperature) {
        switch(temperature.toLowerCase()) {
            case 'hot':
                return 'fa-fire';
            case 'iced':
                return 'fa-snowflake';
            default:
                return 'fa-thermometer-half';
        }
    }

    // Render size options for a menu item
    function renderSizeOptions(sizes) {
        return sizes.map(size => `
            <div class="size-option" 
                 data-size-id="${size.MenuItemSize_ID}"
                 data-price="${size.MenuItemSize_Price}"
                 data-size-name="${size.MenuItemSize_SizeName}"
                 data-temperature="${size.MenuItemSize_IsHot}">
                <span class="temp-badge ${size.MenuItemSize_IsHot.toLowerCase()}">
                    <i class="fas ${getTemperatureIcon(size.MenuItemSize_IsHot)} me-1"></i>
                    ${size.MenuItemSize_IsHot}
                </span>
                <div class="size-details">
                    <span class="size-name">${size.MenuItemSize_SizeName}</span>
                    <span class="size-price">₱${parseFloat(size.MenuItemSize_Price).toFixed(2)}</span>
                </div>
                <i class="fas fa-chevron-right ms-2 text-muted"></i>
            </div>
        `).join('');
    }

    // Handle size selection
    function handleSizeSelection(event) {
        const sizeOption = event.currentTarget;
        const menuItemCard = sizeOption.closest('.menu-item-card');
        const itemId = menuItemCard.dataset.itemId;
        const sizeId = sizeOption.dataset.sizeId;
        const price = parseFloat(sizeOption.dataset.price);
        const sizeName = sizeOption.dataset.sizeName;
        const temperature = sizeOption.dataset.temperature;
        const itemName = menuItemCard.querySelector('.menu-item-name').textContent;

        addToCart({
            itemId,
            sizeId,
            itemName,
            sizeName,
            temperature,
            price,
            quantity: 1
        });
    }

    // Add item to cart
    function addToCart(item) {
        const existingItem = cart.find(cartItem => 
            cartItem.itemId === item.itemId && 
            cartItem.sizeId === item.sizeId
        );

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push(item);
        }

        updateCartDisplay();
    }

    // Update cart display
    function updateCartDisplay() {
        cartItems.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.itemName}</div>
                    <div class="cart-item-size">
                        ${item.sizeName} 
                        <span class="temp-badge ${item.temperature.toLowerCase()}">
                            <i class="fas ${getTemperatureIcon(item.temperature)} me-1"></i>
                            ${item.temperature}
                        </span>
                    </div>
                    <div class="cart-item-price">₱${(item.price * item.quantity).toFixed(2)}</div>
                </div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn minus" data-item-id="${item.itemId}" data-size-id="${item.sizeId}">-</button>
                    <span>${item.quantity}</span>
                    <button class="quantity-btn plus" data-item-id="${item.itemId}" data-size-id="${item.sizeId}">+</button>
                    <button class="btn btn-sm btn-danger ms-2" onclick="removeFromCart('${item.itemId}', '${item.sizeId}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

        // Add event listeners for quantity buttons
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', handleQuantityChange);
        });

        updateCartTotals();
    }

    // Handle quantity change
    function handleQuantityChange(event) {
        const btn = event.currentTarget;
        const itemId = btn.dataset.itemId;
        const sizeId = btn.dataset.sizeId;
        const isPlus = btn.classList.contains('plus');

        const cartItem = cart.find(item => 
            item.itemId === itemId && 
            item.sizeId === sizeId
        );

        if (cartItem) {
            if (isPlus) {
                cartItem.quantity += 1;
            } else {
                cartItem.quantity -= 1;
                if (cartItem.quantity <= 0) {
                    cart = cart.filter(item => 
                        !(item.itemId === itemId && item.sizeId === sizeId)
                    );
                }
            }
            updateCartDisplay();
        }
    }

    // Remove item from cart
    window.removeFromCart = function(itemId, sizeId) {
        cart = cart.filter(item => 
            !(item.itemId === itemId && item.sizeId === sizeId)
        );
        updateCartDisplay();
    };

    // Update cart totals
    function updateCartTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = 0; // Implement discount logic as needed
        const total = subtotal - discount;

        subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
        discountElement.textContent = `₱${discount.toFixed(2)}`;
        totalElement.textContent = `₱${total.toFixed(2)}`;
    }

    // Handle search
    function handleSearch(event) {
        const searchTerm = event.target.value.toLowerCase();
        const menuItems = document.querySelectorAll('.menu-item-card');

        menuItems.forEach(item => {
            const itemName = item.querySelector('.menu-item-name').textContent.toLowerCase();
            const itemCategory = item.querySelector('.menu-item-category').textContent.toLowerCase();
            
            if (itemName.includes(searchTerm) || itemCategory.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Initialize Bootstrap modals
    const emptyCartModal = new bootstrap.Modal(document.getElementById('emptyCartModal'));
    const clearCartModal = new bootstrap.Modal(document.getElementById('clearCartModal'));

    // Handle checkout
    function handleCheckout() {
        if (cart.length === 0) {
            emptyCartModal.show();
            return;
        }

        // Implement checkout logic
        console.log('Proceeding to checkout with items:', cart);
        // Add your checkout implementation here
    }

    // Clear cart confirmation
    function clearCart() {
        clearCartModal.show();
    }

    // Handle clear cart confirmation
    document.getElementById('confirmClearCart').addEventListener('click', function() {
        cart = [];
        updateCartDisplay();
        clearCartModal.hide();
    });

    // Add animation class to modal icons
    document.getElementById('emptyCartModal').addEventListener('show.bs.modal', function() {
        const icon = this.querySelector('.fa-shopping-cart');
        icon.classList.add('animate__animated', 'animate__bounceIn');
    });

    document.getElementById('clearCartModal').addEventListener('show.bs.modal', function() {
        const icon = this.querySelector('.fa-exclamation-triangle');
        icon.classList.add('animate__animated', 'animate__shakeX');
    });
});
