<?php
require_once 'database_customer.php';
require_once 'get_menu_items.php';
require_once 'get_best_sellers.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - SINCO CAFE</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Category CSS -->
    <link rel="stylesheet" href="css/category.css">
    <!-- Out of Stock CSS -->
    <link rel="stylesheet" href="css/out-of-stock.css">
    <!-- Best Seller CSS -->
    <link rel="stylesheet" href="css/best-seller.css">
    <!-- Alert Modals CSS -->
    <link rel="stylesheet" href="css/alert-modals.css">
    <!-- Size Temperature CSS -->
    <link rel="stylesheet" href="css/size-temperature.css">
    <!-- Mobile Layout CSS -->
    <link rel="stylesheet" href="css/mobile-layout.css">
    <!-- Modal Actions CSS -->
    <link rel="stylesheet" href="css/modal-actions.css">
    <!-- Order Type CSS -->
    <link rel="stylesheet" href="css/order-type.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add this line in the head section after other CSS links -->
    <link rel="stylesheet" href="css/modal-actions.css">
</head>
<body>
    <!-- Mobile Phone Container -->
    <div class="mobile-container">
        <div class="mobile-content">
            <!-- Header -->
            <header class="header">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-auto text-center">
                            <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
                            <p class="header-tagline">Where Good Coffee Starts</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Search Bar -->
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="search-container">
                            <input type="text" class="search-input" placeholder="Search">
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row g-0">
                    <!-- Categories Sidebar -->
                    <div class="col-auto categories-wrapper">
                        <div class="categories">
                            <div class="category-section">
                                <div class="category-header">CATEGORIES</div>
                                <a href="#" class="category-item active" data-category="all">
                                    <i class="fas fa-th-large"></i>
                                    <span>ALL ITEMS</span>
                                </a>
                            </div>
                            <div class="category-section">
                                <div class="category-header">DRINKS</div>
                                <a href="#" class="category-item" data-category="Traditional Coffee">
                                    <img src="resources/menu-items/traditional-coffee-icon.png" alt="Traditional Coffee">
                                    <span>TRADITIONAL COFFEE</span>
                                </a>
                                <a href="#" class="category-item" data-category="Coffee">
                                    <img src="resources/menu-items/coffee-icon.png" alt="Coffee">
                                    <span>COFFEE</span>
                                </a>
                                <a href="#" class="category-item" data-category="Non Coffee">
                                    <img src="resources/menu-items/non-coffee-icon.png" alt="Non-Coffee">
                                    <span>NON COFFEE</span>
                                </a>
                                <a href="#" class="category-item" data-category="Mocktail">
                                    <img src="resources/menu-items/mocktails.png" alt="Mocktail">
                                    <span>MOCKTAIL</span>
                                </a>
                            </div>

                            <div class="category-section">
                                <div class="category-header">FOODS</div>
                                <a href="#" class="category-item" data-category="Pastries">
                                    <img src="resources/menu-items/pastries-icon.png" alt="Pastries">
                                    <span>PASTRIES</span>
                                </a>
                                <a href="#" class="category-item" data-category="Snacks">
                                    <img src="resources/menu-items/snacks-icon.png" alt="Snacks">
                                    <span>SNACKS</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items Section-->
                    <div class="col menu-content">
                        <div class="menu-section">
                            <div class="section-title">ALL ITEMS</div>
                            <div class="row row-cols-2 g-2" id="menu-items-container">
                                <!-- Menu items will be dynamically loaded here -->
                                <?php displayMenuItems(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="cart-section">
                <p class="text-center mb-2">List Of Ordered Items: <span id="order-count">0</span></p>
                <button class="btn btn-success w-100" onclick="proceedToCheckout()">Proceed To Checkout</button>
            </div>

            <!-- Item Modal -->
            <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="item-name"></h5>
                            <button type="button" class="close-modal" data-bs-dismiss="modal">×</button>
                        </div>
                        <div class="modal-body">
                            <img src="" alt="Item Image" id="item-image" class="modal-item-image">
                            <p id="item-price" class="modal-item-price"></p>
                            <p id="item-description" class="modal-item-description"></p>
                            
                            <!-- Quantity Controls -->
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="adjustQuantity(-1)">-</button>
                                <span id="quantity">1</span>
                                <button class="quantity-btn" onclick="adjustQuantity(1)">+</button>
                            </div>

                            <!-- Size Options -->
                            <div class="options-group">
                                <div class="size-option">
                                    <button class="option-btn active" onclick="selectSize('Uno', this)">Uno</button>
                                </div>
                                <div class="size-option">
                                    <button class="option-btn" onclick="selectSize('Dos', this)">Dos</button>
                                </div>
                                <div class="size-option">
                                    <button class="option-btn" onclick="selectSize('Tres', this)">Tres</button>
                                </div>
                                <div class="size-option">
                                    <button class="option-btn" onclick="selectSize('Quatro', this)">Quatro</button>
                                </div>
                                <div class="size-option">
                                    <button class="option-btn" onclick="selectSize('Sinco', this)">Sinco</button>
                                </div>
                            </div>
                            
                            <!-- Order Type Options -->
                            <div class="order-type-group">
                                <div class="order-type-option">
                                    <button class="order-type-btn" onclick="selectOrderType('Dine-in', this)">Dine In</button>
                                </div>
                                <div class="order-type-option">
                                    <button class="order-type-btn" onclick="selectOrderType('Take-out', this)">Take Out</button>
                                </div>
                            </div>

                            <div class="modal-actions">
                                <!-- Cancel Button -->
                                <button class="btn-cancel" data-bs-dismiss="modal">×</button>
                                <!-- Add to Card Button -->
                                <button class="btn-confirm" onclick="addToCart()">✓</button> 
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modals -->
     <!-- Alert for Order Type Options -->
    <div class="modal fade alert-modal" id="orderTypeAlert" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Type Required</h5>
                    <button type="button" class="close-modal" data-bs-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle mb-3" style="font-size: 3rem;"></i>
                    <p>Please select an order type (Dine In or Take Out)</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Alert for Empty Check Out List -->
    <div class="modal fade alert-modal" id="emptyCartAlert" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Empty Cart</h5>
                    <button type="button" class="close-modal" data-bs-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-shopping-cart mb-3" style="font-size: 3rem;"></i>
                    <p>Please add items to your cart before proceeding to checkout</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="javascript/menu.js"></script>
    <!-- Size Handler -->
    <script src="javascript/show-temperature.js"></script>
    <!-- Alert System -->
    <script src="javascript/alert-modals.js"></script>
</body>
</html>
