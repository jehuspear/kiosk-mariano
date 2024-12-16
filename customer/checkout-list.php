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
    <style>
        .mobile-container {
            max-width: 450px;
            margin: 0 auto;
            background-color: #fff;
            min-height: 100vh;
            position: relative;
            padding-bottom: 160px; /* Space for bottom section */
        }

        .header {
            background-color: #000;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }

        .logo-image {
            max-width: 150px;
            margin-bottom: 0.5rem;
        }

        .header-tagline {
            font-size: 0.9rem;
            margin: 0;
        }

        .order-list {
            padding: 1rem;
        }

        .order-item {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details {
            flex-grow: 1;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .item-size {
            font-size: 0.9rem;
            color: #666;
        }

        .item-price {
            font-weight: bold;
            color: #000;
        }

        .order-type {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .quantity-btn {
            background: #eee;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .quantity-btn:hover {
            background: #ddd;
        }

        .bottom-section {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 450px;
            background: #fff;
            padding: 1rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .total-amount {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: right;
        }

        .action-buttons {
            display: grid;
            gap: 0.5rem;
        }

        .btn-continue {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 0.75rem;
            border-radius: 5px;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .btn-continue:hover {
            background: #218838;
        }

        .btn-cancel {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 0.75rem;
            border-radius: 5px;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .btn-cancel:hover {
            background: #c82333;
        }

        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .add-more-items {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: #007bff;
            text-decoration: none;
            border: 2px dashed #ccc;
            border-radius: 10px;
            margin: 1rem;
            transition: all 0.2s;
        }

        .add-more-items:hover {
            color: #0056b3;
            border-color: #0056b3;
            background: #f8f9fa;
        }

        .add-more-items i {
            margin-right: 0.5rem;
        }

        /* Toast Message */
        .toast-message {
            position: fixed;
            bottom: 160px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            z-index: 1000;
            animation: fadeInOut 3s ease-in-out;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translate(-50%, 20px); }
            15% { opacity: 1; transform: translate(-50%, 0); }
            85% { opacity: 1; transform: translate(-50%, 0); }
            100% { opacity: 0; transform: translate(-50%, -20px); }
        }
    </style>
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
            <div class="total-order-count" style="text-align: left; position:inherit; font-size: 12px;">
                Total Number of Order Item: <br>
                <span style="margin-left: 70px; color: #000; font-weight:700;font-size: 18px;"><?php echo $totalQuantity; ?></span>
            </div>

            <div class="total-amount">
                Item total: ₱<?php echo number_format($totalAmount, 2); ?>
            </div>
            <div class="action-buttons">
                <button class="btn-continue" onclick="window.location.href='modeofpayment.php'">Continue</button>
                <button class="btn-cancel" onclick="cancelOrder()">Cancel Order</button>
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

        function cancelOrder() {
            if (confirm('Are you sure you want to cancel your order?')) {
                fetch('cancel_order.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'menu.php';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to cancel order');
                });
            }
        }
    </script>
</body>
</html>
