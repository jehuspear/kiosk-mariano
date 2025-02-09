<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get today's date range in Manila time
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

// Get low stock items
include 'database_admin.php';
$low_stock_query = "SELECT MenuItem_Name, MenuItem_TotalStocks 
                    FROM menuitem 
                    WHERE MenuItem_TotalStocks <= 10 
                    ORDER BY MenuItem_TotalStocks ASC 
                    LIMIT 5";
$low_stock_result = $conn->query($low_stock_query);
$low_stock_items = [];
while ($row = $low_stock_result->fetch_assoc()) {
    $low_stock_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SINCO CAFE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="Css-admin/dashboard-dark.css">
    <link rel="stylesheet" href="Css-admin/reports.css">

</head>
<body>
    <div class="wrapper">
        <?php 
        require_once 'includes/sidebar.php';
        renderSidebar('reports');
        ?>

        <div class="main-content">
            <!-- Mobile Menu Toggle -->
            <div class="mobile-menu-toggle d-lg-none">
                <button class="btn btn-dark" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="dashboard-container">
                <div class="main-chart-area">
                    <!-- Customer Count Section -->
                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Customer Count</h3>
                            <div class="chart-controls">
                                <select class="period-select" data-chart-type="customer">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                <input type="date" class="date-input" data-chart-type="customer">
                            </div>
                        </div>
                        <canvas id="customerChart" class="chart-canvas"></canvas>
                    </div>
                    <!-- Top Products Section -->
                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Top 5 Best-Selling Products</h3>
                        </div>
                        <canvas id="topProductsChart" class="chart-canvas"></canvas>
                    </div>
                </div>

                <div class="side-panels">
                    <!-- Products Sold Panel -->
                    <div class="stat-panel">
                        <div class="stat-title">Products Sold</div>
                        <div class="stat-value" id="productsSold">0</div>
                    </div>

                    <!-- Revenue Panel -->
                    <div class="stat-panel">
                        <div class="stat-title">Revenue</div>
                        <div class="stat-value" id="totalRevenue">₱0.00</div>

                        <div class="revenue-details">
                            <div class="revenue-item">
                                <span class="revenue-label">Cash Sales</span>
                                <span class="revenue-value" id="cashTotal">₱0.00</span>
                            </div>
                            <div class="revenue-item">
                                <span class="revenue-label">GCash Sales</span>
                                <span class="revenue-value" id="gcashTotal">₱0.00</span>
                            </div>
                            <div class="revenue-item total-sales">
                                <span class="revenue-label">Total Sales</span>
                                <span class="revenue-value" id="grandTotal">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Items Panel -->
                    <div class="low-stock-panel">
                        <div class="low-stock-title">
                            <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                        </div>
                        <?php if (!empty($low_stock_items)): ?>
                            <?php foreach ($low_stock_items as $item): ?>
                                <div class="low-stock-item">
                                    <span class="item-name"><?php echo $item['MenuItem_Name']; ?></span>
                                    <span class="stock-count"><?php echo $item['MenuItem_TotalStocks']; ?> left</span>
                                </div>
                            <?php endforeach; ?>
                            <div class="stock-warning">
                                These items need to be restocked soon!
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                No items are running low on stock.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set PHP's Manila time for JavaScript to use
        window.manilaTime = '<?php echo date('Y-m-d'); ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="Javascript-admin/mobile-menu.js"></script>
    <script src="Javascript-admin/dashboard.js"></script>
</body>
</html>
