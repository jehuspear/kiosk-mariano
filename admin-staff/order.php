<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orderscreen</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/order.css">
    </head>
    <body>
        <div class="wrapper">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="logo">
                    <h2>SINCO CAFE</h2>
                </div>
                <ul class="nav">
                    <li><a href="login.php"><i class="fa-solid fa-user"></i> Login</a></li>
                    <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
                    <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
                    <li><a href="orderlist.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
                    <li class="active"><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
                    <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
                    <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Dashboard</a></li>
                    <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
                    <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
                </ul>
            </div>
            <!-- Main Content -->
            <div class="main-content">
                <div class="text-wrapper-10">Pending Orders</div>
                <div class="navbar">
                    <div class="navbar-item">Product ID</div>
                    <div class="navbar-item">Order No</div>
                    <div class="navbar-item">Name</div>
                    <div class="navbar-item">Eating Option</div>
                    <div class="navbar-item">Order</div>
                    <div class="navbar-item">Payment</div>
                    <div class="navbar-item">Date</div>
                    <div class="navbar-item">Discount</div>
                    <div class="navbar-item">Total</div>
                    <div class="navbar-item">Status</div>
                </div>

                <!-- Order Rows -->
                <div class="order">
                    <div class="order-item">001<br />003</div>
                    <div class="order-item">224</div>
                    <div class="order-item">Juan</div>
                    <div class="order-item">Dine In</div>
                    <div class="order-item">2 x Salted Caramel 22oz<br />2 x Cafe Mocha 22oz</div>
                    <div class="order-item">Cash</div>
                    <div class="order-item">12/10/24</div>
                    <div class="order-buttons">
                        <button class="button add-button">Add</button>
                        <button class="button add-button">Add</button>
                    </div>
                    <div class="order-item">₱480</div>
                    <div class="order-buttons">
                        <button class="button done-button" data-order-id="001">Done</button>
                    </div>
                </div>

                <div class="order">
                    <div class="order-item">002</div>
                    <div class="order-item">223</div>
                    <div class="order-item">Balong</div>
                    <div class="order-item">Takeout</div>
                    <div class="order-item">1 x Spanish Latte 22oz</div>
                    <div class="order-item">GCash</div>
                    <div class="order-item">12/10/24</div>
                    <div class="order-buttons">
                        <button class="button add-button">Add</button>
                    </div>
                    <div class="order-item">₱120</div>
                    <div class="order-buttons">
                        <button class="button done-button" data-order-id="002">Done</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Is the order complete?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="noButton" data-bs-dismiss="modal">No</button>
                        <button type="button" class="btn btn-success" id="yesButton">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Discount -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discountModalLabel">Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="discount-buttons">
                    <button type="button" class="btn btn-primary me-2" data-discount="10">10%</button>
                    <button type="button" class="btn btn-primary me-2" data-discount="15">15%</button>
                    <button type="button" class="btn btn-primary me-2" data-discount="20">20%</button>
                    <button type="button" class="btn btn-secondary" id="customDiscountButton">Custom</button>
                </div>
                <div id="customDiscountInput" class="mt-3" style="display: none;">
                    <label for="customDiscountValue" class="form-label">Enter Custom Discount (%)</label>
                    <input type="number" id="customDiscountValue" class="form-control" placeholder="e.g., 25">
                    <button type="button" class="btn btn-success mt-2" id="applyCustomDiscount">Apply</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- JavaScript -->
        <script>
            // Add click event listeners to "Done" buttons
            document.querySelectorAll('.done-button').forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.getAttribute('data-order-id');
                    console.log('Order ID:', orderId); // Optional: Log the order ID
                    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    modal.show();
                });
            });
        </script>
    </body>
</html>

<!-- Bootstrap JS -->
<script src="Javascript-admin/order.js"></script>
</body>
</html>
