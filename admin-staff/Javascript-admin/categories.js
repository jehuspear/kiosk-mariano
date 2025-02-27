document.addEventListener('DOMContentLoaded', function() {
    // Load categories when the page loads
    loadCategories();
    
    // Add event listeners for form submissions
    document.getElementById('addCategoryForm').addEventListener('submit', addCategory);
    document.getElementById('editCategoryForm').addEventListener('submit', updateCategory);
    document.getElementById('confirmDeleteBtn').addEventListener('click', deleteCategory);
});

// Global variable to store the category ID for deletion
let categoryToDelete = null;

// Function to load categories from the database
function loadCategories() {
    fetch('api/get_categories.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const tableBody = document.getElementById('categoriesTableBody');
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No categories found</td></tr>';
                return;
            }
            
            data.forEach(category => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${category.Category_ID}</td>
                    <td>${category.Category_Name}</td>
                    <td class="description-cell">${category.Category_Description || ''}</td>
                    <td class="actions-cell">
                        <button class="btn btn-sm edit-btn action-btn" data-id="${category.Category_ID}" 
                                data-name="${category.Category_Name}" 
                                data-description="${category.Category_Description || ''}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm delete-btn action-btn" data-id="${category.Category_ID}" 
                                data-name="${category.Category_Name}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // Add event listeners to edit and delete buttons
            addActionButtonListeners();
        })
        .catch(error => {
            showAlert('Error loading categories: ' + error.message, 'danger');
        });
}

// Function to add event listeners to action buttons
function addActionButtonListeners() {
    // Edit button listeners
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            
            document.getElementById('editCategoryId').value = id;
            document.getElementById('editCategoryName').value = name;
            document.getElementById('editCategoryDescription').value = description;
            
            const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editModal.show();
        });
    });
    
    // Delete button listeners
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            categoryToDelete = id;
            document.getElementById('deleteCategoryName').textContent = name;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
            deleteModal.show();
        });
    });
}

// Function to add a new category
function addCategory(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('addCategoryForm'));
    
    fetch('api/add_category.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
            modal.hide();
            
            // Reset the form
            document.getElementById('addCategoryForm').reset();
            
            // Show success message
            showAlert('Category added successfully!', 'success');
            
            // Reload categories
            loadCategories();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error adding category: ' + error.message, 'danger');
    });
}

// Function to update a category
function updateCategory(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('editCategoryForm'));
    
    fetch('api/update_category.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
            modal.hide();
            
            // Show success message
            showAlert('Category updated successfully!', 'success');
            
            // Reload categories
            loadCategories();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error updating category: ' + error.message, 'danger');
    });
}

// Function to delete a category
function deleteCategory() {
    if (!categoryToDelete) {
        showAlert('No category selected for deletion', 'danger');
        return;
    }
    
    const formData = new FormData();
    formData.append('categoryId', categoryToDelete);
    
    fetch('api/delete_category.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
            modal.hide();
            
            // Reset the category to delete
            categoryToDelete = null;
            
            // Show success message
            showAlert('Category deleted successfully!', 'success');
            
            // Reload categories
            loadCategories();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error deleting category: ' + error.message, 'danger');
    });
}

// Function to show alert messages
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type} alert-dismissible fade show`;
    alertElement.role = 'alert';
    
    alertElement.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    alertContainer.appendChild(alertElement);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = bootstrap.Alert.getInstance(alertElement);
        if (alert) {
            alert.close();
        } else {
            alertElement.remove();
        }
    }, 5000);
}
