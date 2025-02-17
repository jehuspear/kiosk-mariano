<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database_admin.php';

// Debug information
error_log("Session data: " . print_r($_SESSION, true));
error_log("Current user ID: " . ($_SESSION['user_id'] ?? 'Not set'));
error_log("Current role: " . ($_SESSION['role'] ?? 'Not set'));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting to login page");
    header('Location: login.php');
    exit;
}

// Get user's role
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
error_log("User role for page: " . $userRole);

// Verify database connection
if (!isset($conn)) {
    error_log("Database connection not available");
    die("Database connection failed");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale - SINCO CAFE</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
        }
        .modal.show .modal-dialog {
            transform: scale(1);
        }
        .modal-content {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-body i.fas {
            display: inline-block;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .modal-body i.fa-shopping-cart {
            background-color: #fff3cd;
        }
        .modal-body i.fa-exclamation-triangle {
            background-color: #f8d7da;
        }
    </style>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="Css-admin/pos.css">
    <link rel="stylesheet" href="Css-admin/sidebar.css">
</head>
<body>
    <div class="sidebar-toggle">
        <button id="sidebarToggle" class="btn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <?php 
        include 'includes/sidebar.php';
        renderSidebar('pos'); // Pass 'pos' as the current page
    ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Left Section - Menu Items -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center mb-3">
                                <div class="col">
                                    <h5 class="mb-0">Menu Items</h5>
                                </div>
                                <div class="col">
                                    <div class="search-box">
                                        <input type="text" id="searchInput" class="form-control" placeholder="Search menu items...">
                                        <i class="fas fa-search search-icon"></i>
                                        <button type="button" class="clear-search" id="clearSearch" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="category-nav">
                                        <button class="category-btn active" data-category="all">All Items</button>
                                        <button class="category-btn" data-category="Traditional Coffee">Traditional Coffee</button>
                                        <button class="category-btn" data-category="Coffee">Coffee</button>
                                        <button class="category-btn" data-category="Non-Coffee">Non-Coffee</button>
                                        <button class="category-btn" data-category="Mocktail">Mocktail</button>
                                        <button class="category-btn" data-category="Pastries">Pastries</button>
                                        <button class="category-btn" data-category="Snacks">Snacks</button>
                                    </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="menu-items-container" id="menuItemsContainer">
                                <!-- Menu items will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section - Cart -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Cart</h5>
                            </div>
                            <div id="datetime" class="text-muted"></div>
                            <div class="cashier-info text-muted">
                                Cashier: <?php echo isset($_SESSION['firstname']) ? htmlspecialchars($_SESSION['firstname']) : 'Unknown'; ?>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="cart-items flex-grow-1" id="cartItems">
                                <!-- Cart items will be displayed here -->
                            </div>
                            <div class="cart-summary mt-auto">
                                <!-- Eating Options -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Eating Option</label>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary flex-grow-1 eating-option active" data-option="dine-in">
                                            <i class="fas fa-utensils me-2"></i>Dine-In
                                        </button>
                                        <button class="btn btn-outline-primary flex-grow-1 eating-option" data-option="take-out">
                                            <i class="fas fa-shopping-bag me-2"></i>Take-Out
                                        </button>
                                    </div>
                                </div>

                                <!-- Payment Mode -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mode of Payment</label>
                                    <div class="d-flex gap-2 mb-2">
                                        <button class="btn btn-outline-success flex-grow-1 payment-mode active" data-mode="cash">
                                            <i class="fas fa-money-bill-wave me-2"></i>Cash
                                        </button>
                                        <button class="btn btn-outline-success flex-grow-1 payment-mode" data-mode="gcash">
                                            <i class="fas fa-mobile-alt me-2"></i>GCash
                                        </button>
                                    </div>
                                    <!-- Cash Payment Input -->
                                    <div id="cashPaymentInput" class="payment-input">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="cashAmount" placeholder="Enter cash amount" min="0" step="0.01">
                                        </div>
                                        <div class="d-flex justify-content-between text-muted mb-2">
                                            <small>Change:</small>
                                            <small id="changeAmount">₱0.00</small>
                                        </div>
                                    </div>
                                    <!-- GCash Payment Input -->
                                    <div id="gcashPaymentInput" class="payment-input" style="display: none;">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="referenceNumber" placeholder="Enter last 6 digits of reference #" maxlength="6" pattern="\d{6}">
                                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Totals -->
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">₱0.00</span>
                                </div>

                                <!-- Discount Selection -->
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                                    <select class="form-select mb-2" id="discountType">
                                        <option value="none">No Discount</option>
                                        <option value="senior">Senior Citizen (20%)</option>
                                        <option value="pwd">PWD (20%)</option>
                                        <option value="custom">Custom Discount</option>
                                    </select>
                                    <div id="customDiscountInput" style="display: none;">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" id="customDiscountName" placeholder="Enter discount name (e.g., Partnership)">
                                        </div>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="customDiscountPercent" min="0" max="100" placeholder="Enter percentage">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discount:</span>
                                    <span id="discount">₱0.00</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total:</span>
                                    <span id="total" class="fw-bold">₱0.00</span>
                                </div>

                                <button class="btn btn-primary w-100 mb-2" id="checkoutBtn">
                                    Process Order
                                </button>
                                <button class="btn btn-outline-danger w-100" id="clearCartBtn">
                                    Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Debug information for frontend -->
    <script>
        console.log('Page loaded with session user ID:', <?php echo json_encode($_SESSION['user_id'] ?? null); ?>);
        console.log('User role:', <?php echo json_encode($userRole); ?>);
    </script>
    
    <!-- Modals -->
    <!-- Empty Cart Warning Modal -->
    <div class="modal fade" id="emptyCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="fas fa-shopping-cart text-warning mb-4" style="font-size: 3rem;"></i>
                    <h4 class="modal-title mb-3">Empty Cart</h4>
                    <p class="text-muted">Please add items to cart before checking out.</p>
                    <button type="button" class="btn btn-primary px-4 mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Cart Confirmation Modal -->
    <div class="modal fade" id="clearCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="fas fa-exclamation-triangle text-danger mb-4" style="font-size: 3rem;"></i>
                    <h4 class="modal-title mb-3">Clear Cart</h4>
                    <p class="text-muted">Are you sure you want to clear all items from the cart?</p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmClearCart">Clear Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Validation Modals -->
    <!-- Insufficient Cash Modal -->
    <div class="modal fade" id="insufficientCashModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="fas fa-money-bill-wave text-danger mb-4" style="font-size: 3rem;"></i>
                    <h4 class="modal-title mb-3">Insufficient Cash</h4>
                    <p class="text-muted">Cash amount must be equal to or greater than the total amount.</p>
                    <button type="button" class="btn btn-primary px-4 mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="fas fa-check-circle text-success mb-4" style="font-size: 3rem;"></i>
                    <h4 class="modal-title mb-3">Order Successful!</h4>
                    <p class="text-muted">Your order has been processed successfully.</p>
                    <button type="button" class="btn btn-primary px-4 mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="receiptModalBody">
                    Loading receipt...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invalid Reference Number Modal -->
    <div class="modal fade" id="invalidRefModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="fas fa-hashtag text-warning mb-4" style="font-size: 3rem;"></i>
                    <h4 class="modal-title mb-3">Invalid Reference Number</h4>
                    <p class="text-muted">Please enter the last 6 digits of the GCash reference number.</p>
                    <button type="button" class="btn btn-primary px-4 mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div class="modal fade" id="printPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Print Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="printPreviewContent" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Receipt content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPrint">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script src="Javascript-admin/pos-receipt-printer.js"></script>
    <script src="Javascript-admin/pos.js"></script>
</body>
</html>
