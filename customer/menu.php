<?php
// Add any necessary PHP logic here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - SINCO CAFE</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        header {
            padding: 20px;
            text-align: center;
        }
        .logo-image {
            max-width: 150px;
            height: auto;
            margin-bottom: 1rem;
        }
        .tagline {
            font-size: 1rem;
            letter-spacing: 1px;
            color: #fff;
            font-weight: 300;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="menu-page">
        <!-- Header Section -->
        <header>
            <img src="resources/images/logo.png" alt="SINCO CAFE Logo" class="logo-image">
            <!-- <p class="tagline">Where Good Coffee Starts</p> -->
        </header>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search">
            <button><i class="fas fa-search"></i></button>
        </div>

        <!-- Menu Categories Sidebar -->
        <aside class="categories">
            <ul>
                <li class="category">DRINKS</li>
                <li class="subcategory">
                    <img src="resources/menu-items/traditional-coffee-icon.png" alt="Traditional Coffee">
                    TRADITIONAL COFFEE
                </li>
                <li class="subcategory">
                    <img src="resources/menu-items/coffee-icon.png" alt="Coffee">
                    COFFEE
                </li>
                <li class="subcategory">
                    <img src="resources/menu-items/non-coffee-icon.png" alt="Non-Coffee">
                    NON COFFEE
                </li>
                <li class="subcategory">
                    <img src="resources/menu-items/mocktail-icon.png" alt="Mocktail">
                    MOCKTAIL
                </li>
                <li class="category">FOODS</li>
                <li class="subcategory">
                    <img src="resources/menu-items/pastries-icon.png" alt="Pastries">
                    PASTRIES
                </li>
                <li class="subcategory">
                    <img src="resources/menu-items/snacks-icon.png" alt="Snacks">
                    SNACKS
                </li>
            </ul>
        </aside>

        <!-- Menu Items -->
        <main class="menu-items">
            <div class="menu-category">
                <h3>ICED</h3>
                <div class="menu-grid">
                    <div class="menu-item" onclick="showDetails('SALTED CARAMEL', 120, 'This salted caramel latte is a delicious coffee drink that is sweet, a little salty, and the perfect pick-me-up.')">
                        <img src="resources/menu-items/salted-caramel.jpg" alt="Salted Caramel">
                        <p class="item-name">SALTED CARAMEL</p>
                        <p class="item-price">₱120.00</p>
                    </div>
                    <div class="menu-item" onclick="showDetails('CAFE MOCHA', 110, 'A rich and creamy blend of espresso, chocolate, and milk.')">
                        <img src="resources/menu-items/cafe-mocha.jpg" alt="Cafe Mocha">
                        <p class="item-name">CAFE MOCHA</p>
                        <p class="item-price">₱110.00</p>
                    </div>
                    <div class="menu-item" onclick="showDetails('SPANISH LATTE', 115, 'A smooth and creamy latte with a hint of sweetened condensed milk.')">
                        <img src="resources/menu-items/spanish-latte.jpg" alt="Spanish Latte">
                        <p class="item-name">SPANISH LATTE</p>
                        <p class="item-price">₱115.00</p>
                    </div>
                    <div class="menu-item" onclick="showDetails('AMERICANO', 100, 'A classic espresso diluted with hot water for a rich coffee flavor.')">
                        <img src="resources/menu-items/americano.jpg" alt="Americano">
                        <p class="item-name">AMERICANO</p>
                        <p class="item-price">₱100.00</p>
                    </div>
                </div>
            </div>

            <div class="menu-category">
                <h3>HOT</h3>
                <div class="menu-grid">
                    <div class="menu-item" onclick="showDetails('SPANISH LATTE', 115, 'A smooth and creamy hot latte with sweetened condensed milk.')">
                        <img src="resources/menu-items/hot-spanish-latte.jpg" alt="Hot Spanish Latte">
                        <p class="item-name">SPANISH LATTE</p>
                        <p class="item-price">₱115.00</p>
                    </div>
                    <div class="menu-item" onclick="showDetails('CAFE MOCHA', 110, 'A warming blend of espresso, steamed milk, and rich chocolate.')">
                        <img src="resources/menu-items/hot-cafe-mocha.jpg" alt="Hot Cafe Mocha">
                        <p class="item-name">CAFE MOCHA</p>
                        <p class="item-price">₱110.00</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Order Summary -->
        <footer class="order-summary">
            <p>List of Ordered Items: <span id="order-count">2</span></p>
            <button onclick="proceedToCheckout()">Proceed To Checkout</button>
        </footer>

        <!-- Item Details Modal -->
        <div class="item-details-modal" id="item-details-modal">
            <div class="modal-content">
                <span class="close-modal" onclick="closeDetails()"><i class="fas fa-times"></i></span>
                <img src="" alt="Item Image" id="item-image" class="modal-item-image">
                <h2 id="item-name" class="modal-item-name"></h2>
                <p id="item-price" class="modal-item-price"></p>
                <p id="item-description" class="modal-item-description"></p>

                <div class="size-options">
                    <button>12oz</button>
                    <button>16oz</button>
                    <button class="selected">22oz</button>
                </div>

                <div class="order-type">
                    <button>Dine In</button>
                    <button>Take Out</button>
                </div>

                <button class="add-to-cart">Add to Cart</button>
            </div>
        </div>
    </div>

    <script src="javascript/script.js"></script>
</body>
</html>
