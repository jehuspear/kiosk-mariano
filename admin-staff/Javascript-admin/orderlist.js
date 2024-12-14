
 // JavaScript to handle "View order list" button clicks
    document.querySelectorAll('.view-order').forEach(button => {
      button.addEventListener('click', function () {
        // Get order details from data attributes
        const order = button.getAttribute('data-order');
        const amount = button.getAttribute('data-amount');

        // Set modal content
        document.getElementById('orderDetails').innerText = order;
        document.getElementById('orderAmount').innerText = amount;

        // Show the modal
        const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
        orderModal.show();
      });
    });



    document.addEventListener("DOMContentLoaded", function () {
        // Select all decline and approve buttons
        const declineButtons = document.querySelectorAll(".action-btn.decline");
        const approveButtons = document.querySelectorAll(".action-btn.approve");
        const confirmationText = document.getElementById("confirmationText");
        const confirmActionBtn = document.getElementById("confirmActionBtn");
      
        // Event listener for Decline buttons
        declineButtons.forEach((button) => {
          button.addEventListener("click", () => {
            confirmationText.textContent = "Are you sure you want to remove this order?";
            confirmActionBtn.onclick = () => {
              console.log("Order removed");
              // Add logic to remove the order here
              document.querySelector("#confirmationModal .btn-close").click(); // Close modal
            };
            const modal = new bootstrap.Modal(document.getElementById("confirmationModal"));
            modal.show();
          });
        });
      
        // Event listener for Approve buttons
        approveButtons.forEach((button) => {
          button.addEventListener("click", () => {
            confirmationText.textContent = "Are you sure you want to confirm this order?";
            confirmActionBtn.onclick = () => {
              console.log("Order confirmed");
              // Add logic to confirm the order here
              document.querySelector("#confirmationModal .btn-close").click(); // Close modal
            };
            const modal = new bootstrap.Modal(document.getElementById("confirmationModal"));
            modal.show();
          });
        });
      });
      
