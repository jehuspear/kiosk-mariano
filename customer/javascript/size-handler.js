// Function to reset size buttons to default state (Uno selected)
function resetSizeButtons() {
    document.querySelectorAll('.options-group:first-of-type .option-btn').forEach(btn => {
        // Remove active class from all buttons
        btn.classList.remove('active');
        // Remove any existing temperature badges
        const badge = btn.querySelector('.temp-badge');
        if (badge) badge.remove();
        
        // Make Uno active and add its temperature badge
        if (btn.textContent.trim() === 'Uno') {
            btn.classList.add('active');
            const badge = document.createElement('span');
            badge.className = 'temp-badge hot';
            badge.textContent = 'HOT';
            btn.appendChild(badge);
        }
    });
}

// Function to handle size button click
function handleSizeClick(size, button) {
    // Update selected size
    selectedSize = size;
    
    // Remove active class from all size buttons
    document.querySelectorAll('.options-group:first-of-type .option-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    button.classList.add('active');
    
    // Update price for selected size
    if (currentItemId) {
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
                const price = parseFloat(data.price);
                document.getElementById('item-price').textContent = `₱${price.toFixed(2)}`;
                if (window.currentItem) {
                    window.currentItem.price = price;
                }
            }
        })
        .catch(error => {
            console.error('Error updating price:', error);
        });
    }
}

// Function to reset size buttons when modal opens
function resetSizeButtons() {
    // Reset size buttons and make Uno active
    document.querySelectorAll('.options-group:first-of-type .option-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.trim() === 'Uno') {
            btn.classList.add('active');
        }
    });
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to size buttons
    document.querySelectorAll('.options-group:first-of-type .option-btn').forEach(button => {
        const size = button.textContent.trim();
        button.onclick = function() {
            handleSizeClick(size, this);
        };
    });
    
    // Reset size buttons when modal is shown
    const itemModal = document.getElementById('itemModal');
    if (itemModal) {
        itemModal.addEventListener('show.bs.modal', resetSizeButtons);
    }
});
