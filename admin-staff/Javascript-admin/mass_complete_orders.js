document.addEventListener('DOMContentLoaded', function() {
    const massCompleteBtn = document.getElementById('massCompleteBtn');
    if (massCompleteBtn) {
        massCompleteBtn.addEventListener('click', handleMassComplete);
    }
    
    // Initial fetch of ready tickets
    updateReadyTickets();
    
    // Update ready tickets every 30 seconds
    setInterval(updateReadyTickets, 30000);
});

async function updateReadyTickets() {
    try {
        const response = await fetch('mass_complete_orders.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=fetch'
        });
        
        const data = await response.json();
        const ticketDisplay = document.getElementById('readyTickets');
        
        if (data.success && data.ticket_numbers.length > 0) {
            const ticketHtml = data.ticket_numbers.map(ticket => 
                `<span>#${ticket}</span>`
            ).join(' ');
            ticketDisplay.innerHTML = `Ready to Claim: ${ticketHtml}`;
            ticketDisplay.className = 'ready-tickets';
            document.getElementById('massCompleteBtn').disabled = false;
        } else {
            ticketDisplay.innerHTML = 'No orders ready to complete';
            ticketDisplay.className = 'no-ready-tickets';
            document.getElementById('massCompleteBtn').disabled = true;
        }
    } catch (error) {
        console.error('Error fetching ready tickets:', error);
    }
}

async function handleMassComplete() {
    const massCompleteBtn = document.getElementById('massCompleteBtn');
    const originalText = massCompleteBtn.innerHTML;
    
    try {
        // First fetch the ready orders to show in confirmation
        const response = await fetch('mass_complete_orders.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=fetch'
        });
        
        const data = await response.json();
        
        if (!data.success && data.message === 'No ready orders found to complete') {
            showToast('error', 'No ready orders found to complete');
            return;
        }
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch ready orders');
        }
        
        // Show confirmation modal with ticket numbers
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const ticketList = data.ticket_numbers.map(ticket => `#${ticket}`).join(', ');
        document.getElementById('confirmationText').innerHTML = `
            Are you sure you want to complete the following orders?<br><br>
            <strong>Tickets:</strong> ${ticketList}<br><br>
            Total: ${data.ticket_numbers.length} order${data.ticket_numbers.length > 1 ? 's' : ''}
        `;
        
        confirmationModal.show();
        
        // Get the confirm action button
        const confirmBtn = document.getElementById('confirmActionBtn');
        
        // Create a promise that resolves when the user confirms
        const userConfirmed = await new Promise((resolve) => {
            const handleConfirm = () => {
                confirmBtn.removeEventListener('click', handleConfirm);
                confirmationModal.hide();
                resolve(true);
            };
            
            const handleCancel = () => {
                confirmBtn.removeEventListener('click', handleConfirm);
                confirmationModal.hide();
                resolve(false);
            };
            
            confirmBtn.addEventListener('click', handleConfirm);
            document.querySelector('#confirmationModal .btn-close').addEventListener('click', handleCancel);
            document.querySelector('#confirmationModal .btn-secondary').addEventListener('click', handleCancel);
        });
        
        if (!userConfirmed) return;
        
        // Show loading state
        massCompleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        massCompleteBtn.disabled = true;

        // Perform the update
        const updateResponse = await fetch('mass_complete_orders.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=update'
        });
        
        if (!updateResponse.ok) {
            throw new Error('Network response was not ok');
        }

        const updateData = await updateResponse.json();
        
        if (updateData.success) {
            showToast('success', `Successfully completed ${updateData.affected_rows} order${updateData.affected_rows > 1 ? 's' : ''}`);
            // Refresh the page after 2 seconds
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(updateData.message || 'Failed to update orders');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', error.message || 'Failed to process orders');
        // Reset button state
        massCompleteBtn.innerHTML = originalText;
        massCompleteBtn.disabled = false;
    }
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} mass-complete-toast`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
