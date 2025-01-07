// Function to check and update orders
function checkAndUpdateOrders() {
    fetch('auto_complete_orders.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.updated > 0) {
                // Refresh the page if any orders were updated
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
}

// Check every minute
setInterval(checkAndUpdateOrders, 60000);

// Initial check when page loads
document.addEventListener('DOMContentLoaded', checkAndUpdateOrders);
