<?php
// Add any necessary PHP logic here
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-auto">
                    <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
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
                        <div class="category-header">DRINKS</div>
                        <a href="#" class="category-item">
                            <img src="resources/menu-items/traditional-coffee-icon.png" alt="Traditional Coffee">
                            <span>TRADITIONAL</span>
                        </a>
                        <a href="#" class="category-item">
                            <img src="resources/menu-items/coffee-icon.png" alt="Coffee">
                            <span>COFFEE</span>
                        </a>
                        <a href="#" class="category-item">
                            <img src="resources/menu-items/non-coffee-icon.png" alt="Non-Coffee">
                            <span>NON COFFEE</span>
                        </a>
                        <a href="#" class="category-item">
                            <img src="resources/menu-items/mocktails.png" alt="Mocktail">
                            <span>MOCKTAIL</span>
                        </a>
                    </div>

                    <div class="category-section">
                        <div class="category-header">FOODS</div>
                        <a href="#" class="category-item">
                            <img src="resources/menu-items/pastries-icon.png" alt="Pastries">
                            <span>PASTRIES</span>
                        </a>
                        <a href="#" class="category-item">
                            <img src="resources/menu-items/snacks-icon.png" alt="Snacks">
                            <span>SNACKS</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="col menu-content">
                <div class="menu-section">
                    <div class="section-title bg-black">ICED</div>
                    <div class="row row-cols-2 g-3">
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal" 
                                 onclick="showDetails('SALTED CARAMEL', 120, 'This salted caramel latte is a delicious coffee drink that is sweet, a little salty, and the perfect pick-me-up.')">
                                <img src="resources/menu-items/salted-caramel.jpg" alt="Salted Caramel">
                                <div class="item-details">
                                    <p class="item-name">SALTED CARAMEL</p>
                                    <p class="item-price">₱120.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('CAFE MOCHA', 110, 'A rich and creamy blend of espresso, chocolate, and milk.')">
                                <img src="resources/menu-items/cafe-mocha.jpg" alt="Cafe Mocha">
                                <div class="item-details">
                                    <p class="item-name">CAFE MOCHA</p>
                                    <p class="item-price">₱110.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('SPANISH LATTE', 115, 'A smooth and creamy latte with a hint of sweetened condensed milk.')">
                                <img src="resources/menu-items/spanish-latte.jpg" alt="Spanish Latte">
                                <div class="item-details">
                                    <p class="item-name">SPANISH LATTE</p>
                                    <p class="item-price">₱115.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('AMERICANO', 100, 'A classic espresso diluted with hot water.')">
                                <img src="resources/menu-items/americano.jpg" alt="Americano">
                                <div class="item-details">
                                    <p class="item-name">AMERICANO</p>
                                    <p class="item-price">₱100.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('COFFEE LATTE', 110, 'A smooth blend of espresso and steamed milk.')">
                                <img src="resources/menu-items/spanish-latte.jpg" alt="Coffee Latte">
                                <div class="item-details">
                                    <p class="item-name">COFFEE LATTE</p>
                                    <p class="item-price">₱110.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('CAPPUCCINO', 110, 'Equal parts espresso, steamed milk, and milk foam.')">
                                <img src="resources/menu-items/cafe-mocha.jpg" alt="Cappuccino">
                                <div class="item-details">
                                    <p class="item-name">CAPPUCCINO</p>
                                    <p class="item-price">₱110.00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-section mt-4">
                    <div class="section-title bg-black">HOT</div>
                    <div class="row row-cols-2 g-3">
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('SPANISH LATTE', 115, 'A smooth and creamy hot latte with sweetened condensed milk.')">
                                <img src="resources/menu-items/hot-spanish-latte.jpg" alt="Spanish Latte">
                                <div class="item-details">
                                    <p class="item-name">SPANISH LATTE</p>
                                    <p class="item-price">₱115.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('SALTED CARAMEL', 120, 'A warming blend of espresso, caramel, and steamed milk.')">
                                <img src="resources/menu-items/hot-cafe-mocha.jpg" alt="Salted Caramel">
                                <div class="item-details">
                                    <p class="item-name">SALTED CARAMEL</p>
                                    <p class="item-price">₱120.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('CAFE MOCHA', 110, 'A warming blend of espresso, steamed milk, and rich chocolate.')">
                                <img src="resources/menu-items/hot-cafe-mocha.jpg" alt="Cafe Mocha">
                                <div class="item-details">
                                    <p class="item-name">CAFE MOCHA</p>
                                    <p class="item-price">₱110.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="menu-item" data-bs-toggle="modal" data-bs-target="#itemModal"
                                 onclick="showDetails('AMERICANO', 100, 'A classic hot espresso diluted with hot water.')">
                                <img src="resources/menu-items/hot-cafe-mocha.jpg" alt="Americano">
                                <div class="item-details">
                                    <p class="item-name">AMERICANO</p>
                                    <p class="item-price">₱100.00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Section -->
    <div class="cart-section">
        <p class="text-center mb-2">List Of Ordered Items: <span id="order-count">2</span></p>
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
                    
                    <div class="options-group">
                        <button class="option-btn" onclick="selectSize('12oz', this)">12oz</button>
                        <button class="option-btn" onclick="selectSize('16oz', this)">16oz</button>
                        <button class="option-btn active" onclick="selectSize('22oz', this)">22oz</button>
                    </div>
                    
                    <div class="options-group">
                        <button class="option-btn" onclick="selectOrderType('Dine In', this)">Dine In</button>
                        <button class="option-btn" onclick="selectOrderType('Take Out', this)">Take Out</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="javascript/script.js"></script>
</body>
</html>
