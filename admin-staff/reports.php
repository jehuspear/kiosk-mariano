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
    <title>Dashboard - SINCO CAFE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="Css-admin/reports.css">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .chart-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #1565c0;
            margin: 0;
        }
        .chart-canvas {
            width: 100%;
            height: 300px;
        }
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        @media (max-width: 992px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }
        .period-select {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            color: #1565c0;
            font-size: 0.9em;
        }
        .period-select:focus {
            outline: none;
            border-color: #1565c0;
        }
        .revenue-totals {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 15px 0;
        }
        .total-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #ddd;
        }
        .total-item.cash {
            border-left-color: #4caf50;
        }
        .total-item.gcash {
            border-left-color: #2196f3;
        }
        .total-item.grand-total {
            border-left-color: #1565c0;
            background: #e3f2fd;
            font-weight: bold;
            font-size: 1.1em;
        }
        .total-label {
            color: #666;
        }
        .total-value {
            font-size: 1.1em;
            color: #1565c0;
        }
        .grand-total .total-label,
        .grand-total .total-value {
            color: #1565c0;
        }
    </style>
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
                <!-- Customer Count Section -->
                <div class="chart-section">
                    <div class="chart-header">
                        <h3 class="chart-title">Customer Count</h3>
                        <select class="period-select" data-chart-type="customer">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <canvas id="customerChart" class="chart-canvas"></canvas>
                </div>

                <div class="chart-grid">
                    <!-- Products Sold Section -->
                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Products Sold</h3>
                            <select class="period-select" data-chart-type="product">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <canvas id="productChart" class="chart-canvas"></canvas>
                    </div>

                    <!-- Revenue Section -->
                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Revenue</h3>
                            <select class="period-select" data-chart-type="revenue" onchange="updateRevenueTotals(this.value)">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="revenue-totals">
                            <div class="total-item cash">
                                <div class="total-label">Total Cash Sales</div>
                                <div class="total-value" id="cashTotal">₱0.00</div>
                            </div>
                            <div class="total-item gcash">
                                <div class="total-label">Total GCash Sales</div>
                                <div class="total-value" id="gcashTotal">₱0.00</div>
                            </div>
                            <div class="total-item grand-total">
                                <div class="total-label">Total Sales</div>
                                <div class="total-value" id="grandTotal">₱0.00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products Section -->
                <div class="chart-section">
                    <div class="chart-header">
                        <h3 class="chart-title">Top 5 Best-Selling Products</h3>
                    </div>
                    <canvas id="topProductsChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="Javascript-admin/mobile-menu.js"></script>
    <script src="Javascript-admin/dashboard.js"></script>
</body>
</html>
