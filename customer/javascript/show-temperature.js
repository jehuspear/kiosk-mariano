// Function to show temperature badges for a menu item
async function showTemperatures(itemId) {
    try {
        const response = await fetch(`get_size_temperature.php?itemId=${itemId}`);
        const data = await response.json();
        
        if (data.success) {
            // Get all size option containers
            const sizeOptions = document.querySelectorAll('.size-option');
            if (!sizeOptions.length) {
                console.error('Size options not found');
                return;
            }

            // Add temperature badges to each size option
            sizeOptions.forEach(sizeOption => {
                // Get the button inside this size option
                const button = sizeOption.querySelector('.option-btn');
                if (!button) return;

                // Remove any existing badge
                const existingBadge = sizeOption.querySelector('.temp-badge');
                if (existingBadge) {
                    existingBadge.remove();
                }
                
                // Find temperature for this size
                const size = button.textContent.trim();
                const sizeInfo = data.sizes.find(s => s.size === size);
                
                if (sizeInfo) {
                    // Create temperature badge
                    const badge = document.createElement('span');
                    badge.className = `temp-badge ${sizeInfo.temperature === 'HOT' ? 'hot' : 'cold'}`;
                    badge.textContent = sizeInfo.temperature;
                    
                    // Add badge to size option div (not the button)
                    sizeOption.appendChild(badge);
                }
            });
        }
    } catch (error) {
        console.error('Error fetching temperatures:', error);
    }
}

// Function to update temperature badge when size is selected
function updateTemperatureBadge(button, size) {
    // Find the parent size-option div
    const sizeOption = button.closest('.size-option');
    if (!sizeOption) return;

    // Remove any existing badge
    const existingBadge = sizeOption.querySelector('.temp-badge');
    if (existingBadge) {
        existingBadge.remove();
    }
    
    // Get temperature from database mapping
    const isHot = {
        'Uno': true,    // HOT
        'Dos': true,    // HOT
        'Tres': false,  // COLD
        'Quatro': false,// COLD
        'Sinco': false  // COLD
    }[size];
    
    // Create and add temperature badge
    const badge = document.createElement('span');
    badge.className = `temp-badge ${isHot ? 'hot' : 'cold'}`;
    badge.textContent = isHot ? 'HOT' : 'COLD';
    
    // Add badge to size-option div (not the button)
    sizeOption.appendChild(badge);
}

// Initialize when modal is shown
document.addEventListener('DOMContentLoaded', function() {
    const itemModal = document.getElementById('itemModal');
    if (itemModal) {
        // Show temperatures when modal opens
        itemModal.addEventListener('shown.bs.modal', function() {
            const itemId = this.getAttribute('data-item-id');
            if (itemId) {
                // Delay slightly to ensure buttons are rendered
                setTimeout(() => {
                    showTemperatures(itemId);
                }, 100);
            }
        });
        
        // Reset temperatures when modal closes
        itemModal.addEventListener('hidden.bs.modal', function() {
            const badges = document.querySelectorAll('.temp-badge');
            badges.forEach(badge => badge.remove());
        });
    }
});
