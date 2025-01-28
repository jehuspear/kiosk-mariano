<div class="wrapper">
    <?php
    session_start();

    // Check if user is not logged in
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pending Orders</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/sidebar.css">
        <link rel="stylesheet" href="Css-admin/order.css">
        <link rel="stylesheet" href="Css-admin/search_order.css">
        <link rel="stylesheet" href="Css-admin/admin-modal.css">
        <link rel="stylesheet" href="Css-admin/reports.css">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>

    <body>
        <div class="wrapper">
            <?php 
            require_once 'includes/sidebar.php';
            renderSidebar('reports');
            ?>
            <!-- Main Content -->
            <div class="main-content">
                <!-- Mobile Menu Toggle -->
                <div class="mobile-menu-toggle d-lg-none">
                    <button class="btn btn-dark" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <!-- Customer Count Section -->
                <div class="customer-count">
                    <h3>Customer Count</h3>
                    <canvas id="customerChart"></canvas>
                    <div class="customercategory">
                        </select>
                    </div>
                </div>

                <!-- Stats Section (Cups Sold & Revenue Cards) -->
                <div class="stats">
                    <div class="card">
                        <h3>Cups Sold</h3>
                        <p>51</p>
                        <canvas id="cupsChart"></canvas>
                        <div class="cupscategory">
                            <select id="categoryDropdown">
                                <option>Daily</option>
                                <option>Weekly</option>
                                <option>Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Revenue</h3>
                        <p>₱10,000</p>
                        <canvas id="revenueChart"></canvas>
                        <div class="revenuecategory">
                            <select id="categoryDropdown">
                                <option>Daily</option>
                                <option>Weekly</option>
                                <option>Monthly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Best Selling Section -->
                <div class="best-selling">
                    <h3>Best Selling</h3>
                    <canvas id="bestSellingChart"></canvas>
                    <script src="Javascript-admin/reports.js"></script>
                    <div class="bestsellingcategory">
                        <select id="categoryDropdown">
                            <option>Daily</option>
                            <option>Weekly</option>
                            <option>Monthly</option>
                        </select>
                    </div>
                </div>

                <!-- Categories Section -->
                <div class="categories">
                    <h3>Select category</h3>
                    <canvas id="categoriesChart"></canvas>
                    <div class="categoriescategory">
                        <select id="categoryDropdown">
                            <option>TRADITIONAL COFFEE</option>
                            <option>COFFEE</option>
                            <option>NON COFFEE</option>
                            <option>MOCKTAILS</option>
                            <option>PASTRIES</option>
                            <option>SNACKS</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <script src="Javascript-admin/reports.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    </body>

    </html>
