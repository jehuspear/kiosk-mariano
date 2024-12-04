document.addEventListener('DOMContentLoaded', () => {
    const approveButtons = document.querySelectorAll('.approve');
    const declineButtons = document.querySelectorAll('.decline');

    approveButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            alert('Order Approved!');
        });
    });

    declineButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            alert('Order Declined!');
        });
    });
});
