let deleteItemId = null;

function openModal(url) {
    document.getElementById('modalOverlay').classList.add('show');
    document.getElementById('modalContainer').classList.add('show');
    document.getElementById('modalIframe').src = url;
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('show');
    document.getElementById('modalContainer').classList.remove('show');
    document.getElementById('modalIframe').src = '';
    // Reload the parent page to refresh the menu items
    window.location.reload();
}

function editMenuItem(menuItemId) {
    openModal('edit_menu_item.php?id=' + menuItemId);
}

function deleteMenuItem(menuItemId) {
    deleteItemId = menuItemId;
    document.getElementById('modalOverlay').classList.add('show');
    document.getElementById('deleteModal').classList.add('show');
}

function cancelDelete() {
    deleteItemId = null;
    document.getElementById('modalOverlay').classList.remove('show');
    document.getElementById('deleteModal').classList.remove('show');
}

function confirmDelete() {
    if (deleteItemId) {
        window.location.href = 'delete_menu_item.php?id=' + deleteItemId;
    }
}

function addMenuItem() {
    openModal('add_menu_item.php');
}

// Close modal when clicking outside
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        if (document.getElementById('deleteModal').classList.contains('show')) {
            cancelDelete();
        } else {
            closeModal();
        }
    }
});

// Close modal with escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('deleteModal').classList.contains('show')) {
            cancelDelete();
        } else {
            closeModal();
        }
    }
});