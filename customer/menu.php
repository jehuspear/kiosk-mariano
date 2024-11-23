<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Mariano Cafe</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="menu-page">
        <!-- Header Section -->
        <header>
            <img src="resources/images/logo.png" alt="Mariano Cafe Logo" class="logo">
            <p class="tagline">Where Good Coffee Starts</p>
        </header>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search">
            <button><i class="fas fa-search"></i></button>
        </div>

        <!-- Menu Categories Sidebar -->
        <aside class="categories">
            <ul>
                <li class="category">Drinks</li>
                <li class="subcategory">Traditional Coffee</li>
                <li class="subcategory">Coffee</li>
                <li class="subcategory">Non-Coffee</li>
                <li class="subcategory">Mocktail</li>
                <li class="category">Foods</li>
                <li class="subcategory">Pastries</li>
                <li class="subcategory">Snacks</li>
            </ul>
        </aside>

        <!-- Menu Items -->
        <main class="menu-items">
            <div class="menu-category">
                <h3>Iced</h3>
                <div class="menu-grid">
                    <div class="menu-item" onclick="showDetails('Salted Caramel', 120, 'This salted caramel latte is a delicious coffee drink that is sweet, a little salty, and the perfect pick-me-up.')">
                        <img src="salted-caramel.png" alt="Salted Caramel">
                        <p>Salted Caramel</p>
                        <p>₱120.00</p>
                    </div>
                    <div class="menu-item out-of-stock">
                        <img src="cafe-mocha.png" alt="Cafe Mocha">
                        <p>Cafe Mocha</p>
                        <p>Out of Stock</p>
                    </div>
                    <!-- Add more items here -->
                </div>
            </div>
            <!-- Add more categories as needed -->
        </main>

        <!-- Order Summary -->
        <footer class="order-summary">
            <p>List of Ordered Items: <span id="order-count">0</span></p>
            <button onclick="proceedToCheckout()">Proceed To Checkout</button>
        </footer>

        <!-- Item Details Modal -->
        <div class="item-details-modal" id="item-details-modal">
            <div class="modal-content">
                <span class="close-modal" onclick="closeDetails()">×</span>
                <img src="" alt="Item Image" id="item-image">
                <h2 id="item-name"></h2>
                <p id="item-price"></p>
                <p id="item-description"></p>

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

    <script src="script.js"></script>
</body>
</html>

