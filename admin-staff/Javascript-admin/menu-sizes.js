// Function to show success/error messages
function showMessage(message, isSuccess = true) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${isSuccess ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.querySelector('.form-container').insertBefore(alertDiv, document.querySelector('form'));
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Function to validate form data
function validateFormData(data) {
    if (!data.sizeName || data.sizeName.length > 50) {
        showMessage('Please enter a valid size name (max 50 characters)', false);
        return false;
    }
    if (!data.price || data.price <= 0) {
        showMessage('Please enter a valid price', false);
        return false;
    }
    if (!data.temperatureType) {
        showMessage('Please select a temperature type', false);
        return false;
    }
    if (!data.stock || data.stock < 0) {
        showMessage('Please enter a valid stock quantity', false);
        return false;
    }
    return true;
}

// Function to update temperature type styling
function updateTemperatureTypeStyle(select) {
    if (!select) return;
    
    const value = select.value.toLowerCase();
    select.className = `form-control temperature-type-select ${value}`;
    
    // Update icon based on temperature type
    const icon = select.parentElement.querySelector('.temperature-icon');
    if (icon) {
        icon.remove();
    }
    
    const newIcon = document.createElement('span');
    newIcon.className = 'temperature-icon';
    switch (value) {
        case 'hot':
            newIcon.innerHTML = '<i class="fas fa-fire"></i>';
            break;
        case 'iced':
            newIcon.innerHTML = '<i class="fas fa-snowflake"></i>';
            break;
        case 'normal':
            newIcon.innerHTML = '<i class="fas fa-thermometer-half"></i>';
            break;
    }
    select.parentElement.insertBefore(newIcon, select);
}

// Function to create new size
async function createMenuItemSize(menuItemId) {
    const sizeName = document.getElementById('newSizeName').value;
    const price = document.getElementById('newSizePrice').value;
    const temperatureType = document.getElementById('newTemperatureType').value;
    const stock = document.getElementById('newSizeStock').value;

    const formData = {
        sizeName,
        price: parseFloat(price),
        temperatureType,
        stock: parseInt(stock)
    };

    if (!validateFormData(formData)) {
        return;
    }

    try {
        const response = await fetch('manage_menu_size.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'create',
                menuItemId: menuItemId,
                ...formData
            })
        });

        const data = await response.json();
        showMessage(data.message, data.success);
        
        if (data.success) {
            // Reset form and hide it
            hideAddSizeForm();
            // Reload the page to show the new size
            location.reload();
        }
    } catch (error) {
        showMessage('Error creating size: ' + error.message, false);
    }
}

// Function to update size
async function updateMenuItemSize(sizeId) {
    const sizeName = document.getElementById(`size_name_${sizeId}`).value;
    const price = document.getElementById(`price_${sizeId}`).value;
    const temperatureType = document.getElementById(`temperature_type_${sizeId}`).value;
    const stock = document.getElementById(`stock_${sizeId}`).value;

    const formData = {
        sizeName,
        price: parseFloat(price),
        temperatureType,
        stock: parseInt(stock)
    };

    if (!validateFormData(formData)) {
        return;
    }

    try {
        const response = await fetch('manage_menu_size.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'update',
                sizeId: sizeId,
                ...formData
            })
        });

        const data = await response.json();
        showMessage(data.message, data.success);
        
        if (data.success) {
            // Update the temperature type styling
            const temperatureSelect = document.getElementById(`temperature_type_${sizeId}`);
            updateTemperatureTypeStyle(temperatureSelect);
            
            // Update the row's appearance
            const row = document.querySelector(`tr[data-size-id="${sizeId}"]`);
            if (row) {
                row.style.transition = 'background-color 0.3s ease';
                row.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 1000);
            }
        }
    } catch (error) {
        showMessage('Error updating size: ' + error.message, false);
    }
}

// Function to delete size
async function deleteMenuItemSize(sizeId) {
    if (!confirm('Are you sure you want to delete this size? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch('manage_menu_size.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'delete',
                sizeId: sizeId
            })
        });

        const data = await response.json();
        showMessage(data.message, data.success);
        
        if (data.success) {
            // Remove the size row from the table with animation
            const row = document.querySelector(`tr[data-size-id="${sizeId}"]`);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                }, 300);
            }
        }
    } catch (error) {
        showMessage('Error deleting size: ' + error.message, false);
    }
}

// Function to show add size form
function showAddSizeForm() {
    const addSizeForm = document.getElementById('addSizeForm');
    if (addSizeForm) {
        addSizeForm.style.display = 'block';
        // Focus on the first input
        document.getElementById('newSizeName').focus();
    }
}

// Function to hide add size form
function hideAddSizeForm() {
    const addSizeForm = document.getElementById('addSizeForm');
    if (addSizeForm) {
        // Animate form hiding
        addSizeForm.style.opacity = '0';
        addSizeForm.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            addSizeForm.style.display = 'none';
            addSizeForm.style.opacity = '1';
            addSizeForm.style.transform = 'translateY(0)';
            
            // Reset form fields
            document.getElementById('newSizeName').value = '';
            document.getElementById('newSizePrice').value = '';
            document.getElementById('newTemperatureType').value = '';
            document.getElementById('newSizeStock').value = '';
            
            // Reset temperature type styling
            updateTemperatureTypeStyle(document.getElementById('newTemperatureType'));
        }, 300);
    }
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for update buttons
    document.querySelectorAll('.update-size-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sizeId = this.dataset.sizeId;
            updateMenuItemSize(sizeId);
        });
    });

    // Add event listeners for delete buttons
    document.querySelectorAll('.delete-size-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sizeId = this.dataset.sizeId;
            deleteMenuItemSize(sizeId);
        });
    });

    // Add event listeners for temperature type changes
    document.querySelectorAll('.temperature-type-select').forEach(select => {
        select.addEventListener('change', function() {
            updateTemperatureTypeStyle(this);
        });
        // Set initial style
        updateTemperatureTypeStyle(select);
    });

    // Set initial style for new temperature type select
    const newTemperatureType = document.getElementById('newTemperatureType');
    if (newTemperatureType) {
        newTemperatureType.addEventListener('change', function() {
            updateTemperatureTypeStyle(this);
        });
    }

    // Add form animations
    const addSizeForm = document.getElementById('addSizeForm');
    if (addSizeForm) {
        addSizeForm.style.transition = 'all 0.3s ease';
    }
});
