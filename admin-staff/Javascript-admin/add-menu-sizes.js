let sizeIndex = 0;

// Function to add a new size row
function addSizeRow() {
    const tbody = document.getElementById('sizesTableBody');
    const template = document.getElementById('sizeRowTemplate');
    const clone = template.content.cloneNode(true);
    
    // Replace {index} placeholder with actual index
    const elements = clone.querySelectorAll('[name*="{index}"]');
    elements.forEach(element => {
        element.name = element.name.replace('{index}', sizeIndex);
    });
    
    // Add row with animation
    const row = clone.querySelector('tr');
    row.style.opacity = '0';
    row.style.transform = 'translateY(-10px)';
    tbody.appendChild(clone);
    
    // Trigger reflow
    row.offsetHeight;
    
    // Add animation
    row.style.transition = 'all 0.3s ease';
    row.style.opacity = '1';
    row.style.transform = 'translateY(0)';
    
    // Add temperature type styling
    const temperatureSelect = row.querySelector('.temperature-type-select');
    if (temperatureSelect) {
        temperatureSelect.addEventListener('change', function() {
            updateTemperatureTypeStyle(this);
        });
    }
    
    // Focus on the size name input
    row.querySelector('input[type="text"]').focus();
    
    sizeIndex++;
}

// Function to remove a size row
function removeSizeRow(button) {
    const row = button.closest('tr');
    
    // Add remove animation
    row.style.transition = 'all 0.3s ease';
    row.style.opacity = '0';
    row.style.transform = 'translateX(20px)';
    
    // Remove row after animation
    setTimeout(() => {
        row.remove();
        
        // Check if there are no more rows
        const tbody = document.getElementById('sizesTableBody');
        if (tbody.children.length === 0) {
            addSizeRow(); // Add a new row if none exist
        }
    }, 300);
}

// Function to update temperature type styling
function updateTemperatureTypeStyle(select) {
    if (!select) return;
    
    // Remove all temperature type classes first
    select.classList.remove('hot', 'iced', 'normal');
    
    // Get the selected value and convert to lowercase
    const value = select.value.toLowerCase();
    
    // Keep the base classes and add the temperature type class if a value is selected
    select.className = `form-control temperature-type-select${value ? ' ' + value : ''}`;
}

// Function to validate form data
function validateSizeRow(row) {
    const nameInput = row.querySelector('input[name*="[name]"]');
    const priceInput = row.querySelector('input[name*="[price]"]');
    const tempSelect = row.querySelector('select[name*="[temperature_type]"]');
    const stockInput = row.querySelector('input[name*="[stock]"]');
    
    let isValid = true;
    
    // Reset validation states
    [nameInput, priceInput, tempSelect, stockInput].forEach(input => {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    });
    
    // Validate size name
    if (!nameInput.value || nameInput.value.length > 50) {
        isValid = false;
        nameInput.classList.add('is-invalid');
        addInvalidFeedback(nameInput, 'Please enter a valid size name (max 50 characters)');
    }
    
    // Validate price
    if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
        isValid = false;
        priceInput.classList.add('is-invalid');
        addInvalidFeedback(priceInput, 'Please enter a valid price');
    }
    
    // Validate temperature type
    if (!tempSelect.value) {
        isValid = false;
        tempSelect.classList.add('is-invalid');
        addInvalidFeedback(tempSelect, 'Please select a temperature type');
    }
    
    // Validate stock
    if (!stockInput.value || parseInt(stockInput.value) < 0) {
        isValid = false;
        stockInput.classList.add('is-invalid');
        addInvalidFeedback(stockInput, 'Please enter a valid stock quantity');
    }
    
    return isValid;
}

// Function to add invalid feedback message
function addInvalidFeedback(input, message) {
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    input.parentNode.appendChild(feedback);
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Add initial size row
    addSizeRow();
    
    // Add form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        const sizesTable = document.getElementById('sizesTableBody');
        if (sizesTable.children.length === 0) {
            event.preventDefault();
            alert('Please add at least one size');
            addSizeRow();
            return false;
        }
        
        // Validate all size rows
        let isValid = true;
        sizesTable.querySelectorAll('.size-row').forEach(row => {
            if (!validateSizeRow(row)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            event.preventDefault();
            return false;
        }
    });
    
    // Add category change handler
    const categorySelect = document.getElementById('category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const category = this.value;
            const rows = document.querySelectorAll('.size-row');
            
            rows.forEach(row => {
                const tempSelect = row.querySelector('.temperature-type-select');
                if (tempSelect && !tempSelect.value) {
                    // Set default temperature based on category
                    if (['Traditional Coffee', 'Coffee'].includes(category)) {
                        tempSelect.value = 'Hot';
                    } else if (['Mocktail', 'Non-Coffee'].includes(category)) {
                        tempSelect.value = 'Iced';
                    } else if (['Pastries', 'Snacks'].includes(category)) {
                        tempSelect.value = 'Normal';
                    }
                    updateTemperatureTypeStyle(tempSelect);
                }
            });
        });
    }
});
