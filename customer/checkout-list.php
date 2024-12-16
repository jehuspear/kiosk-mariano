<?php
require_once 'database_customer.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart array in session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Function to get menu item details
function getMenuItemDetails($menuItemId) {
    global $conn;
    $sql = "SELECT m.*, ms.MenuItemSize_Price, ms.MenuItemSize_Size 
            FROM menuitem m 
            JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID 
            WHERE m.MenuItem_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $menuItemId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Calculate total amount
$totalAmount = 0;
$totalQuantity = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
    $totalQuantity += $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SINCO CAFE</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/checkout-list.css">
    <link rel="stylesheet" href="css/cancel-order-modal.css">
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <header class="header">
            <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
            <p class="header-tagline">Where Good Coffee Starts</p>
            <h5 class="mt-2 mb-0">Order List</h5>
        </header>

        <!-- Order List -->
        <div class="order-list">
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <p>Your cart is empty</p>
                </div>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="order-item">
                        <div class="d-flex">
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
                            <div class="item-details ms-3">
                                <div class="item-name"><?php echo $item['name']; ?></div>
                                <div class="item-size"><?php echo $item['size']; ?></div>
                                <div class="item-price">₱<?php echo number_format($item['price'], 2); ?></div>
                                <div class="order-type"><?php echo $item['orderType']; ?></div>
                                <div class="quantity-control">
                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $index; ?>, -1)">-</button>
                                    <span><?php echo $item['quantity']; ?></span>
                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $index; ?>, 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Add More Items Button -->
            <a href="menu.php" class="add-more-items">
                <i class="fas fa-plus"></i>
                Add more items
            </a>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <!-- Total Order Count Display -->
            <div class="total-order-count">
                Total Number of Order Item: <br>
                <span class="total-quantity"><?php echo $totalQuantity; ?></span>
            </div>

            <div class="total-amount">
                Item total: ₱<?php echo number_format($totalAmount, 2); ?>
            </div>
            <div class="action-buttons">
                <button class="btn-continue" onclick="window.location.href='modeofpayment.php'">Continue</button>
                <button class="btn-cancel" onclick="showCancelOrderModal()">Cancel Order</button>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <i class="fas fa-exclamation-triangle modal-icon"></i>
                    <p class="modal-message">Are you sure you want to cancel your order?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancelOrder()">Yes, Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Message Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastMessage" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function showToast(message) {
            // Remove existing toast if any
            const existingToast = document.querySelector('.toast-message');
            if (existingToast) {
                existingToast.remove();
            }

            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = message;
            document.body.appendChild(toast);

            // Remove toast after animation
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        function updateQuantity(index, change) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `index=${index}&change=${change}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                    showToast('Cart updated');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update cart');
            });
        }

        function showCancelOrderModal() {
            const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            modal.show();
        }

        function confirmCancelOrder() {
            fetch('cancel_order.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'menu.php';
                } else {
                    showToast('Failed to cancel order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to cancel order');
            });
        }
    </script>
</body>
</html>
