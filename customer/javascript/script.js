// script.js
function startApp() {
    window.location.href = 'menu.php'; // Redirect to the menu page
}

// Show Item Details Modal
function showDetails(name, price, description) {
    document.getElementById('item-details-modal').style.display = 'flex';
    document.getElementById('item-name').textContent = name;
    document.getElementById('item-price').textContent = `₱${price}.00`;
    document.getElementById('item-description').textContent = description;
}

// Close Item Details Modal
function closeDetails() {
    document.getElementById('item-details-modal').style.display = 'none';
}

// Proceed to Checkout
function proceedToCheckout() {
    window.location.href = 'checkout.php';
}
