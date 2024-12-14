// Get the modal
var modal = document.getElementById("signOutModal");

// Get the button that opens the modal
var signOutBtn = document.getElementById("signOutBtn");

// Get the <span> element that closes the modal
var closeModal = document.getElementById("closeModal");

// Get the buttons inside the modal
var yesBtn = document.getElementById("yesBtn");
var noBtn = document.getElementById("noBtn");

// When the user clicks the "Sign Out?" button, open the modal
signOutBtn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks the "X" (close) button, close the modal
closeModal.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks "Yes", redirect to login.php
yesBtn.onclick = function() {
  window.location.href = "login.php";  // Redirect to login page
}

// When the user clicks "No", close the modal
noBtn.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
