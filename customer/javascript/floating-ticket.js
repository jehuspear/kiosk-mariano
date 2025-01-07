// Function to update the floating ticket status
function updateFloatingTicketStatus() {
    fetch('get_order_status.php')
        .then(response => response.json())
        .then(data => {
            const floatingTicket = document.querySelector('.floating-ticket');
            if (floatingTicket && data.status) {
                // Remove all status classes
                floatingTicket.classList.remove(
                    'status-pending',
                    'status-preparing',
                    'status-readytoclaim',
                    'status-completed',
                    'status-cancelled'
                );
                // Add the current status class
                floatingTicket.classList.add(`status-${data.status}`);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Update status when page loads
document.addEventListener('DOMContentLoaded', updateFloatingTicketStatus);

// Update status every 30 seconds
setInterval(updateFloatingTicketStatus, 30000);
