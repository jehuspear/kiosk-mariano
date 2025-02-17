document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap modals
    const emptyCartModal = new bootstrap.Modal(document.getElementById('emptyCartModal'));
    const clearCartModal = new bootstrap.Modal(document.getElementById('clearCartModal'));
    const insufficientCashModal = new bootstrap.Modal(document.getElementById('insufficientCashModal'));
    const invalidRefModal = new bootstrap.Modal(document.getElementById('invalidRefModal'));

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

    // Initialize state
    let cart = [];
    let eatingOption = 'dine-in';
    let paymentMode = 'cash';
    let discountType = 'none';
    let customDiscountPercent = 0;
    let customDiscountName = '';
    let cashAmount = 0;
    let referenceNumber = '';

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

    // Event listeners for eating options
    document.querySelectorAll('.eating-option').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.eating-option').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            eatingOption = this.dataset.option;
        });
    });

    // Event listeners for payment mode
    document.querySelectorAll('.payment-mode').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.payment-mode').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            paymentMode = this.dataset.mode;
            
            document.getElementById('cashPaymentInput').style.display = paymentMode === 'cash' ? 'block' : 'none';
            document.getElementById('gcashPaymentInput').style.display = paymentMode === 'gcash' ? 'block' : 'none';
            
            document.getElementById('cashAmount').value = '';
            document.getElementById('referenceNumber').value = '';
            document.getElementById('changeAmount').textContent = '₱0.00';
            document.getElementById('changeAmount').className = '';
            cashAmount = 0;
            referenceNumber = '';
        });
    });

    // Function to update change amount
    function updateChangeAmount() {
        const total = parseFloat(totalElement.textContent.replace('₱', ''));
        const change = cashAmount - total;
        
        const changeElement = document.getElementById('changeAmount');
        changeElement.textContent = `₱${Math.abs(change).toFixed(2)}`;
        changeElement.className = change >= 0 ? 'text-success' : 'text-danger';
    }

    // Cash amount input handler
    document.getElementById('cashAmount').addEventListener('input', function() {
        cashAmount = parseFloat(this.value) || 0;
        updateChangeAmount();
    });

    // GCash reference number input handler
    document.getElementById('referenceNumber').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
        referenceNumber = this.value;
    });

    // Event listener for discount type
    document.getElementById('discountType').addEventListener('change', function() {
        discountType = this.value;
        const customInput = document.getElementById('customDiscountInput');
        customInput.style.display = discountType === 'custom' ? 'block' : 'none';
        if (discountType !== 'custom') {
            customDiscountName = '';
            document.getElementById('customDiscountName').value = '';
        }
        updateCartTotals();
    });

    // Event listeners for custom discount
    document.getElementById('customDiscountPercent').addEventListener('input', function() {
        customDiscountPercent = Math.min(100, Math.max(0, parseFloat(this.value) || 0));
        updateCartTotals();
    });

    document.getElementById('customDiscountName').addEventListener('input', function() {
        customDiscountName = this.value.trim();
        updateCartTotals();
    });

    // Handle checkout
    checkoutBtn.addEventListener('click', async () => {
        if (cart.length === 0) {
            emptyCartModal.show();
            return;
        }

        const subtotal = parseFloat(subtotalElement.textContent.replace('₱', ''));
        const total = parseFloat(totalElement.textContent.replace('₱', ''));
        const discountAmount = parseFloat(discountElement.textContent.replace('₱', '').split(' ')[0]);

        if (paymentMode === 'cash') {
            if (cashAmount < total) {
                insufficientCashModal.show();
                return;
            }
        } else if (paymentMode === 'gcash') {
            if (referenceNumber.length !== 6) {
                invalidRefModal.show();
                return;
            }
        }

        // Prepare order data
        const orderData = {
            items: cart.map(item => ({
                itemId: parseInt(item.itemId, 10) || 0,
                sizeId: parseInt(item.sizeId, 10) || 0,
                itemName: String(item.itemName || ''),
                sizeName: String(item.sizeName || ''),
                quantity: parseInt(item.quantity, 10) || 0,
                price: parseFloat(item.price) || 0
            })),
            totalAmount: parseFloat(subtotal) || 0,
            finalAmount: parseFloat(total) || 0,
            discountType: discountType || 'none',
            discountAmount: parseFloat(discountAmount) || 0,
            paymentMethod: paymentMode === 'cash' ? 'Cash' : 'GCash', // Normalize payment method case
            eatingOption: eatingOption,
            cashAmount: parseFloat(cashAmount || 0),
            referenceNumber: referenceNumber || '',
            customDiscountName: customDiscountName || '',
            customDiscountPercent: parseFloat(customDiscountPercent || 0)
        };

        try {
            console.log('Sending order data:', orderData);
            
            const response = await fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            let result;
            const responseText = await response.text();
            console.log('Raw server response:', responseText);

            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse server response:', e);
                throw new Error('Invalid server response format');
            }

            if (!response.ok) {
                throw new Error(result.message || `Server error: ${response.status}`);
            }

            if (!result.success) {
                throw new Error(result.message || 'Order processing failed');
            }
            
            console.log('Order processed successfully:', result);

            // Load receipt in modal
            const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            
            // Load receipt content
            try {
                const receiptResponse = await fetch(`get_receipt.php?order_id=${result.orderDetails.orderId}`);
                const receiptData = await receiptResponse.json();
                
                if (receiptData.success) {
                    // Create iframe to load view_receipt.php
                    const receiptHtml = `
                        <iframe 
                            src="view_receipt.php?order_id=${result.orderDetails.orderId}" 
                            style="width: 100%; height: 600px; border: none;"
                            id="receiptFrame"
                            onload="this.contentWindow.focus()"
                        ></iframe>
                    `;
                    
                    document.getElementById('receiptModalBody').innerHTML = receiptHtml;
                    
                    // Show receipt modal
                    receiptModal.show();

                    // Update print button to use the iframe's print function
                    const printButton = document.querySelector('#receiptModal .btn-primary');
                    printButton.onclick = () => {
                        const frame = document.getElementById('receiptFrame');
                        frame.contentWindow.print();
                    };
                    
                    // When receipt modal is closed, show success modal
                    document.getElementById('receiptModal').addEventListener('hidden.bs.modal', function () {
                        successModal.show();
                    }, { once: true });
                }
            } catch (error) {
                console.error('Error loading receipt:', error);
            }

            // Clear cart and reset form
            cart = [];
            updateCartDisplay();
            document.getElementById('cashAmount').value = '';
            document.getElementById('referenceNumber').value = '';
            document.getElementById('discountType').value = 'none';
            document.getElementById('customDiscountInput').style.display = 'none';
            document.getElementById('customDiscountName').value = '';
            document.getElementById('customDiscountPercent').value = '';
        } catch (error) {
            console.error('Error processing order:', error);
            alert(error.message || 'Failed to process order. Please try again.');
        }
    });

    // Clear cart confirmation
    clearCartBtn.addEventListener('click', () => clearCartModal.show());

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

    document.getElementById('insufficientCashModal').addEventListener('show.bs.modal', function() {
        const icon = this.querySelector('.fa-money-bill-wave');
        icon.classList.add('animate__animated', 'animate__shakeX');
    });

    document.getElementById('invalidRefModal').addEventListener('show.bs.modal', function() {
        const icon = this.querySelector('.fa-hashtag');
        icon.classList.add('animate__animated', 'animate__headShake');
    });

    document.getElementById('successModal').addEventListener('show.bs.modal', function() {
        const icon = this.querySelector('.fa-check-circle');
        icon.classList.add('animate__animated', 'animate__bounceIn');
    });

    // Add styles for receipt modal and iframe
    const style = document.createElement('style');
    style.textContent = `
        #receiptModalBody {
            padding: 0;
            height: 600px;
            overflow: hidden;
        }
        #receiptFrame {
            width: 100%;
            height: 100%;
            border: none;
            overflow: hidden;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #receiptFrame {
                visibility: visible;
                width: 58mm !important;
                height: auto !important;
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Load menu items
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
        document.getElementById('datetime').textContent = now.toLocaleString('en-US', options);
    }

    // Update datetime every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Search functionality
    const clearSearchBtn = document.getElementById('clearSearch');

    searchInput.addEventListener('input', function(event) {
        handleSearch(event);
        clearSearchBtn.style.display = this.value ? 'block' : 'none';
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
        this.style.display = 'none';
        handleSearch({ target: searchInput });
    });

    // Category filtering
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterItems();
        });
    });

    function filterItems() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeCategory = document.querySelector('.category-btn.active').dataset.category;
        const menuItems = document.querySelectorAll('.menu-item-card');

        menuItems.forEach(item => {
            const itemName = item.querySelector('.menu-item-name').textContent.toLowerCase();
            const itemCategory = item.querySelector('.menu-item-category').textContent;
            const matchesSearch = itemName.includes(searchTerm);
            const matchesCategory = activeCategory === 'all' || itemCategory === activeCategory;

            item.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
        });
    }

    function handleSearch(event) {
        filterItems();
    }

    // Load menu items from database
    async function loadMenuItems() {
        try {
            const response = await fetch('/kiosk-mariano/admin-staff/get_menu_items.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            if (data.success && data.menuItems && data.menuItems.length > 0) {
                renderMenuItems(data.menuItems);
            } else {
                menuItemsContainer.innerHTML = '<div class="alert alert-info">No menu items available.</div>';
            }
        } catch (error) {
            console.error('Error loading menu items:', error);
            menuItemsContainer.innerHTML = '<div class="alert alert-danger">Failed to load menu items. Please try again.</div>';
        }
    }

    // Render menu items
    function renderMenuItems(menuItems) {
        const menuItemsHtml = menuItems.map(item => `
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
        `).join('');

        menuItemsContainer.innerHTML = menuItemsHtml;

        document.querySelectorAll('.size-option').forEach(option => {
            option.addEventListener('click', handleSizeSelection);
        });
    }

    function getTemperatureIcon(temperature) {
        switch(temperature.toLowerCase()) {
            case 'hot': return 'fa-fire';
            case 'iced': return 'fa-snowflake';
            default: return 'fa-thermometer-half';
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

    // Update cart totals with discount
    function updateCartTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discountPercent = 0;

        switch (discountType) {
            case 'senior':
            case 'pwd':
                discountPercent = 20;
                break;
            case 'custom':
                discountPercent = customDiscountPercent;
                break;
        }

        const discountAmount = (subtotal * discountPercent) / 100;
        const total = subtotal - discountAmount;

        subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
        
        // Display discount with description if available
        let discountText = `₱${discountAmount.toFixed(2)}`;
        if (discountType === 'custom' && customDiscountName) {
            discountText += ` (${customDiscountName})`;
        } else if (discountType === 'senior') {
            discountText += ' (Senior Citizen)';
        } else if (discountType === 'pwd') {
            discountText += ' (PWD)';
        }
        discountElement.textContent = discountText;
        
        totalElement.textContent = `₱${total.toFixed(2)}`;
        
        // Update change amount when total changes due to discount
        if (paymentMode === 'cash') {
            updateChangeAmount();
        }
    }
});
