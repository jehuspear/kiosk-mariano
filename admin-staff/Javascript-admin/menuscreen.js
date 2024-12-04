// Add click event listener to all available status buttons
document.querySelectorAll('.btn.status').forEach(function(button) {
    button.addEventListener('click', function() {
      // Toggle between available and unavailable
      if (button.classList.contains('available')) {
        // Change to unavailable (red)
        button.classList.remove('available');
        button.classList.add('unavailable');
        button.textContent = 'UNAVAILABLE'; // Change text to unavailable
        button.style.backgroundColor = 'red'; // Change button color to red
      } else {
        // Change to available (green)
        button.classList.remove('unavailable');
        button.classList.add('available');
        button.textContent = 'AVAILABLE'; // Change text to available
        button.style.backgroundColor = 'green'; // Change button color to green
      }
    });
  });

// Select all size buttons
const sizeButtons = document.querySelectorAll('.size-btn');

// Add event listeners to toggle the active class
sizeButtons.forEach(button => {
  button.addEventListener('click', () => {
    button.classList.toggle('active'); // Toggle the "active" class
  });
});

  document.addEventListener('DOMContentLoaded', () => {
    const customButton = document.querySelector('.custom-btn');
  
    customButton.addEventListener('click', () => {
      // Replace button content with an input field
      if (!customButton.querySelector('.custom-input')) {
        const currentText = customButton.textContent.trim();
        customButton.innerHTML = `<input type="text" class="custom-input" value="${currentText}" />`;
  
        // Automatically focus the input
        const input = customButton.querySelector('.custom-input');
        input.focus();
  
        // When input loses focus, save the value and revert back to button
        input.addEventListener('blur', () => {
          const newValue = input.value.trim();
          customButton.textContent = newValue || "Custom"; // Fallback to default
        });
  
        // Optional: Save on pressing Enter
        input.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            input.blur(); // Trigger blur to save
          }
        });
      }
    });
  });

// Add click event listener to the Add Menu button
document.querySelector('.btn.add-menu-btn').addEventListener('click', function () {
    // Clear the modal fields before showing it
    document.getElementById('menuName').value = '';
    document.getElementById('menuPrice').value = '';
    document.getElementById('menuDescription').value = '';
    document.getElementById('productId').textContent = '';  // Clear Product ID (if it's a p tag)
    
    // Clear the uploaded image preview
    const imagePreview = document.getElementById("imagePreview");
    const previewContainer = document.getElementById("imagePreviewContainer");
    imagePreview.src = ''; // Remove the image source
    previewContainer.style.display = "none"; // Hide the preview container

    // Use Bootstrap's modal API to show the modal
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
});


  
  // Able to upload image in the modal
  document.getElementById("menuImage").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById("imagePreviewContainer");
    const imagePreview = document.getElementById("imagePreview");

    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            imagePreview.src = e.target.result; // Set the preview image source
            previewContainer.style.display = "block"; // Show the preview container
        };
        
        reader.readAsDataURL(file); // Read the file as a data URL
    } else {
        previewContainer.style.display = "none"; // Hide the preview if no file is selected
    }
});


  















  document.addEventListener("DOMContentLoaded", function () {
    // Menu items from header
    const coffeeTab = document.querySelector(".fa-coffee");
    const otherTabs = document.querySelectorAll(
      ".fa-mug-hot, .fa-wine-glass, .fa-cocktail, .fa-cookie, .fa-pizza-slice"
    );
  
    // Menu cards
    const coffeeCards = document.querySelectorAll(".card");
    const addMenuCard = document.querySelector(".add-menu-card");
  
    function showCoffeeCards() {
      // Keep all cards visible when Traditional Coffee is clicked
      coffeeCards.forEach(card => card.style.display = "block");
      addMenuCard.style.display = "block"; // Ensure the "Add Menu" card stays visible
    }
  
    function hideOtherCards() {
      // Hide all other cards except the "Add Menu" card
      coffeeCards.forEach(card => card.style.display = "none");
      addMenuCard.style.display = "block"; // Ensure the "Add Menu" card stays visible
    }
  
    // Event listener for Traditional Coffee
    coffeeTab.addEventListener("click", function () {
      showCoffeeCards();  // Show all cards when clicking Traditional Coffee
    });
  
    // Event listener for other menu items
    otherTabs.forEach(tab => {
      tab.addEventListener("click", function () {
        hideOtherCards(); // Hide all cards when any other menu item is clicked
      });
    });
  
    // Default behavior: show all cards when the page loads (assuming coffee is active by default)
    showCoffeeCards();
  });

  // Add event listener to each menu item in the header menu
document.querySelectorAll('.header-menu .menu-item').forEach(function(menuItem) {
    menuItem.addEventListener('click', function() {
      // Remove 'active' class from all menu items
      document.querySelectorAll('.header-menu .menu-item').forEach(function(item) {
        item.classList.remove('active');
      });
  
      // Add 'active' class to the clicked menu item
      menuItem.classList.add('active');
    });
  });
  



  // TRIAL CODE OKAY
 // Flag to track if changes were saved
let isSaved = false;
let currentMenuItem = null; // To track the specific menu item being edited

// Add click event listener to the Add Menu button (assuming it's a button per menu item)
document.querySelector('.btn.add-menu-btn').addEventListener('click', function () {
    // Clear the modal fields before showing it
    document.getElementById('menuName').value = '';
    document.getElementById('menuPrice').value = '';
    document.getElementById('menuDescription').value = '';
    document.getElementById('productId').textContent = '';  // Clear Product ID (if it's a p tag)
  
    // Clear the uploaded image preview
    const imagePreview = document.getElementById("imagePreview");
    const previewContainer = document.getElementById("imagePreviewContainer");
    imagePreview.src = ''; // Remove the image source
    previewContainer.style.display = "none"; // Hide the preview container

    // Reset the flag to false when opening the modal
    isSaved = false;

    // Open the modal
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
});

// Add click event listener for "Save Changes" button
document.querySelector('.btn.btn-success').addEventListener('click', function () {
    // Get the input values from the modal form
    const menuName = document.getElementById('menuName').value;
    const menuPrice = document.getElementById('menuPrice').value;
    const menuDescription = document.getElementById('menuDescription').value;
    const productId = document.getElementById('productId').textContent;

    // Ensure that all fields are filled out before saving
    if (menuName && menuPrice && menuDescription && productId) {
        // Save the data for the current menu item (this can be stored in an object or database)
        currentMenuItem = {
            name: menuName,
            price: menuPrice,
            description: menuDescription,
            id: productId
        };

        // Update the button's label or any other specific element if necessary
        // Assuming you have a button for each menu item that displays the name and price
        const menuButton = document.querySelector(`[data-product-id="${productId}"]`); // Example: query based on the product ID
        if (menuButton) {
            menuButton.innerHTML = `${menuName} - ${menuPrice}`;
        }

        // Mark changes as saved
        isSaved = true;

        // Close the modal
        const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        editModal.hide();
    
    }
});

// Add event listener for modal hidden event (when modal is closed without saving)
document.getElementById('editModal').addEventListener('hidden.bs.modal', function () {
    // If the modal was not saved, clear the fields
    if (!isSaved) {
        document.getElementById('menuName').value = '';
        document.getElementById('menuPrice').value = '';
        document.getElementById('menuDescription').value = '';
        document.getElementById('productId').textContent = ''; // Clear Product ID (if it's a p tag)
        
        // Clear the uploaded image preview
        const imagePreview = document.getElementById("imagePreview");
        const previewContainer = document.getElementById("imagePreviewContainer");
        imagePreview.src = ''; // Remove the image source
        previewContainer.style.display = "none"; // Hide the preview container
    }

    // Ensure the backdrop is removed after closing the modal
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop) {
        modalBackdrop.remove(); // Remove backdrop element
    }
});

//Modal for verification of removing a product from the menuscreen
document.addEventListener("DOMContentLoaded", function () {
    // Attach event listener to all delete buttons
    document.querySelectorAll(".btn.btn-danger.action-btn").forEach((deleteButton) => {
      deleteButton.addEventListener("click", function () {
        // Show the delete confirmation modal
        const deleteModal = new bootstrap.Modal(document.getElementById("deleteConfirmationModal"));
        deleteModal.show();
      });
    });
  
    // Handle confirmation button click
    document.getElementById("confirmDelete").addEventListener("click", function () {
       
      });
    });
  
    // modal delete confirmation
document.addEventListener("DOMContentLoaded", function () {
  let targetCard; // Variable to store the card to be removed

  // Attach event listener to all delete buttons
  document.querySelectorAll(".btn.btn-danger.action-btn").forEach((deleteButton) => {
    deleteButton.addEventListener("click", function () {
      // Identify the parent card element
      targetCard = this.closest(".card"); // Adjust ".card" to match your card's class or container
      // Show the delete confirmation modal
      const deleteModal = new bootstrap.Modal(document.getElementById("deleteConfirmationModal"));
      deleteModal.show();
    });
  });

  // Handle "Check" (Yes) button click
  document.getElementById("confirmDelete").addEventListener("click", function () {
    if (targetCard) {
      targetCard.remove(); // Remove the card
      targetCard = null; // Reset the variable to avoid accidental reuse
    }
    // Hide the modal properly
    const deleteModal = bootstrap.Modal.getInstance(document.getElementById("deleteConfirmationModal"));
    deleteModal.hide();

    // Remove any leftover modal backdrop (fixes black screen issue)
    document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());

    // Ensure body class is cleaned up
    document.body.classList.remove("modal-open");
    document.body.style = "";
  });

  // Handle "Exit" button click (close modal when clicked on "Exit" or "X")
  const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
  closeButtons.forEach(button => {
    button.addEventListener("click", function () {
      // Hide the modal
      const deleteModal = bootstrap.Modal.getInstance(document.getElementById("deleteConfirmationModal"));
      deleteModal.hide();

      // Remove any leftover modal backdrop (fixes black screen issue)
      document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());

      // Ensure body class is cleaned up
      document.body.classList.remove("modal-open");
      document.body.style = "";
    });
  });
});


    
    document.addEventListener("DOMContentLoaded", () => {
      const menuImageInput = document.getElementById("menuImage");
      const imagePreviewContainer = document.getElementById("imagePreviewContainer");
      const imagePreview = document.getElementById("imagePreview");
    
      menuImageInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
    
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            imagePreview.src = e.target.result; // Set the preview image's source
            imagePreviewContainer.style.display = "block"; // Show the preview container
          };
          reader.readAsDataURL(file); // Read the file as a Data URL
        } else {
          // If no file is selected, hide the preview
          imagePreviewContainer.style.display = "none";
          imagePreview.src = "";
        }
      });
    });
  

    
    
   








































    document.addEventListener("DOMContentLoaded", () => {
      // Select all edit buttons
      const editButtons = document.querySelectorAll(".action-btn.btn-warning");
    
      // Loop through each button and create a modal dynamically
      editButtons.forEach((button, index) => {
        // Assign unique data attribute to each button
        button.setAttribute("data-bs-target", `#editModal-${index}`);
    
        // Create a unique modal for this button
        const modalTemplate = `
          <div class="modal fade" id="editModal-${index}" tabindex="-1" aria-labelledby="editModalLabel-${index}" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel-${index}">Edit Menu Item</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form>
                    <div class="mb-3">
                      <label for="menuImage-${index}" class="form-label">Upload Image</label>
                      <input type="file" class="form-control" id="menuImage-${index}" accept="image/*">
                    </div>
                    <div class="mb-3" id="imagePreviewContainer-${index}" style="display:none;">
                      <label class="form-label">Preview:</label>
                      <img id="imagePreview-${index}" src="" alt="Image Preview" class="img-fluid" />
                    </div>
                    <div class="mb-3">
                      <label for="menuName-${index}" class="form-label">Name</label>
                      <input type="text" class="form-control" id="menuName-${index}" value="Sample Name">
                    </div>
                    <div class="mb-3">
                      <label for="productId-${index}" class="form-label">Product ID</label>
                      <p id="productId-${index}" class="text-muted">00${index + 1}</p>
                    </div>
                    <div class="cup-sizes">
                      <label class="form-label">Cup Sizes Available</label>
                      <div class="size-buttons">
                        <button type="button" class="size-btn">8oz</button>
                        <button type="button" class="size-btn">12oz</button>
                        <button type="button" class="size-btn">16oz</button>
                        <button type="button" class="size-btn">22oz</button>
                        <button type="button" class="size-btn custom-btn">Custom</button>
                      </div>
                    </div>      
                    <div class="mb-3">
                      <label for="menuPrice-${index}" class="form-label">Price</label>
                      <input type="number" class="form-control" id="menuPrice-${index}" value="120">
                    </div>
                    <div class="mb-3">
                      <label for="menuDescription-${index}" class="form-label">Description</label>
                      <textarea class="form-control" id="menuDescription-${index}" rows="3">Sample Description</textarea>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-success">Save Changes</button>
                </div>
              </div>
            </div>
          </div>
        `;
    
        // Append the modal to the body
        document.body.insertAdjacentHTML("beforeend", modalTemplate);
    
        // Add click listener to the button to open the modal
        button.addEventListener("click", () => {
          const editModal = new bootstrap.Modal(document.getElementById(`editModal-${index}`));
          editModal.show();
        });
      });
    });
    







































































    document.addEventListener("DOMContentLoaded", () => {
      // Modal for adding new menu
      const addMenuModal = new bootstrap.Modal(document.getElementById("addMenuModal"));
      const addMenuButton = document.querySelector(".add-menu-container");
      const addMenuImageInput = document.getElementById("addMenuImage");
      const addImagePreviewContainer = document.getElementById("addImagePreviewContainer");
      const addImagePreview = document.getElementById("addImagePreview");
    
      // Modal for editing menu
      const editModal = new bootstrap.Modal(document.getElementById("editModal"));
      const editMenuImageInput = document.getElementById("menuImage");
      const imagePreviewContainer = document.getElementById("imagePreviewContainer");
      const imagePreview = document.getElementById("imagePreview");
    
      // Open "Add New Menu" Modal
      addMenuButton.addEventListener("click", () => {
        addMenuModal.show(); // Show the add menu modal
      });
    
      // Image preview functionality for adding new menu
      addMenuImageInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
    
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            addImagePreview.src = e.target.result; // Set the preview image's source
            addImagePreviewContainer.style.display = "block"; // Show the preview container
          };
          reader.readAsDataURL(file); // Read the file as a Data URL
        } else {
          addImagePreviewContainer.style.display = "none"; // Hide the preview container if no file is selected
          addImagePreview.src = "";
        }
      });
    
      // Open "Edit Menu" Modal when clicking the edit button
      const editButtons = document.querySelectorAll(".edit-btn");
      editButtons.forEach((button) => {
        button.addEventListener("click", () => {
          editModal.show(); // Show the edit menu modal
        });
      });
    
      // Image preview functionality for editing menu
      editMenuImageInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
    
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            imagePreview.src = e.target.result; // Set the preview image's source
            imagePreviewContainer.style.display = "block"; // Show the preview container
          };
          reader.readAsDataURL(file); // Read the file as a Data URL
        } else {
          imagePreviewContainer.style.display = "none"; // Hide the preview container if no file is selected
          imagePreview.src = "";
        }
      });
    
      // Delete confirmation logic
      const deleteButtons = document.querySelectorAll(".btn btn-danger action-btn");
      deleteButtons.forEach((button) => {
        button.addEventListener("click", () => {
          const deleteConfirmationModal = new bootstrap.Modal(document.getElementById("deleteConfirmationModal"));
          deleteConfirmationModal.show();
        });
      });
    
      // Confirm delete logic
      document.getElementById("confirmDelete").addEventListener("click", () => {
        // Add your delete logic here (e.g., remove the menu item from the DOM or send a request to the server)
        console.log("Menu item deleted.");
      });
    });

    //For dynamic Auto-Generating Product IDs:///////////////////////////////////////////////////////////////////////////////////////
    document.addEventListener("DOMContentLoaded", () => {
      let nextProductId = 1; // Initialize with the next available ID
    
      document.querySelector(".btn-success").addEventListener("click", () => {
        const productIdField = document.getElementById("addProductId");
        productIdField.textContent = `MENU-${String(nextProductId).padStart(3, "0")}`;
        nextProductId++; // Increment for the next menu item
      });
    });
  

    
    




















































    
    

































    
    
    
      
      
      
      
      





    
