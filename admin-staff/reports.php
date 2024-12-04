    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sinco Cafe Dashboard</title>
            <!-- Font Awesome CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <!-- Bootstrap CSS -->
            <link rel="stylesheet" href="Css-admin/bootstrap.min.css">    
            <!-- External CSS Link -->
            <link rel="stylesheet" href="Css-admin/reports.css">
        </head>
        
    <body>
    <div class="dashboard">
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
                <li><a href="order.php"><i class="fa-solid fa-mug-hot"></i> Orders</a></li>
                <li><a href="completed.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
                <li class="active"><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Dashboard</a></li>
                <li><a href="decodingscreen.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
                <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <main class="main-content">
        <div class="customer-count">
            <h3>Customer Count</h3>
            <canvas id="customerChart"></canvas>
                <script src="Javascript-admin/reports.js"></script>
            <div class="customercategory">
                <select id="categoryDropdown">
                <option>Daily</option>
                <option>Weekly</option>
                <option>Monthly</option>S
                </select>
            </div>
        </div>
        <div class="stats">
            <div class="card">
            <h3>Cups Sold</h3>
            <p>51</p>
            <canvas id="cupsChart"></canvas>
            <div class="cupscategory">
                <select id="categoryDropdown">
                    <option>Daily</option>
                    <option>Weekly</option>
                    <option>Monthly</option>S
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
                    <option>Monthly</option>S
                </select>
                </div>
            </div>
        </div>
        <div class="best-selling">
            <h3>Best Selling</h3>
            <canvas id="bestSellingChart"></canvas>
                <script src="Javascript-admin/reports.js"></script>

                
                
            <div class="bestsellingcategory">
                <select id="categoryDropdown">
                <option>Daily</option>
                <option>Weekly</option>
                <option>Monthly</option>S
                </select>
            </div>   
        </div>
        <div class="categories">
            <h3>Select Category</h3>
            <select id="categoryDropdown">
            <option>TRADITIONAL COFFEE</option>
            <option>COFFEE</option>
            <option>NON COFFEE</option>
            <option>MOCKTAILS</option>
            <option>PASTRIES</option>
            <option>SNACKS</option>
            </select>
        </div>  
        </main>
    </div>
    <script src="Javascript-admin/reports.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js
"></script>
    </body>
    </html>