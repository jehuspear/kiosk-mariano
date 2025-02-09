<?php
session_start();
include 'database_admin.php';

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get staff details
$staff_query = "SELECT Staff_FirstName, Staff_MiddleName, Staff_LastName 
                FROM staff 
                WHERE Staff_ID = ?";
$stmt_staff = $conn->prepare($staff_query);
$stmt_staff->bind_param("i", $_SESSION["user_id"]);
$stmt_staff->execute();
$staff_result = $stmt_staff->get_result();
$staff_info = $staff_result->fetch_assoc();

// Get filter parameters
$period = isset($_GET['period']) ? $_GET['period'] : 'daily';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Prepare date ranges based on period
switch($period) {
    case 'daily':
        $startDate = $date . ' 00:00:00';
        $endDate = $date . ' 23:59:59';
        $dateLabel = date('F d, Y', strtotime($date));
        break;
    case 'weekly':
        $startDate = date('Y-m-d 00:00:00', strtotime('monday this week', strtotime($date)));
        $endDate = date('Y-m-d 23:59:59', strtotime('sunday this week', strtotime($date)));
        $dateLabel = 'Week of ' . date('F d, Y', strtotime($startDate));
        break;
    case 'monthly':
        $startDate = date('Y-m-01 00:00:00', strtotime($date));
        $endDate = date('Y-m-t 23:59:59', strtotime($date));
        $dateLabel = date('F Y', strtotime($date));
        break;
    default:
        $startDate = $date . ' 00:00:00';
        $endDate = $date . ' 23:59:59';
        $dateLabel = date('F d, Y', strtotime($date));
}

// Fetch completed orders
$sql = "SELECT o.Order_ID, o.Order_DateTime, o.Order_TicketNumber, 
               o.Order_EatingOption, o.Order_TotalAmount, o.Payment_Method,
               oi.MenuItem_ID, m.MenuItem_Name, oi.OrderItem_CupSize,
               oi.OrderItem_Quantity, oi.OrderItem_Price,
               p.Payment_DiscountType, p.Payment_DiscountAmount,
               p.Payment_CashPaid, p.Payment_Change, p.Payment_ReferenceNumber,
               p.Payment_TotalAmount
        FROM `order` o
        LEFT JOIN orderitem oi ON o.Order_ID = oi.Order_ID
        LEFT JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
        LEFT JOIN payment p ON o.Payment_ID = p.Payment_ID
        WHERE p.Payment_Status = 'Completed'
        AND o.Order_DateTime BETWEEN ? AND ?
        ORDER BY o.Order_DateTime DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Initialize totals
$totalOrders = 0;
$cashSales = 0;
$gcashSales = 0;
$totalSales = 0;

// Store orders in array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['Order_ID'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'datetime' => $row['Order_DateTime'],
            'ticket' => $row['Order_TicketNumber'],
            'eating_option' => $row['Order_EatingOption'],
            'payment_method' => $row['Payment_Method'],
            'subtotal' => $row['Order_TotalAmount'],
            'total' => $row['Payment_TotalAmount'],
            'discount_type' => $row['Payment_DiscountType'],
            'discount_amount' => $row['Payment_DiscountAmount'],
            'cash_paid' => $row['Payment_CashPaid'],
            'change' => $row['Payment_Change'],
            'reference_number' => $row['Payment_ReferenceNumber'],
            'items' => []
        ];
        
        // Update totals
        $totalOrders++;
        if ($row['Payment_Method'] === 'Cash') {
            $cashSales += $row['Payment_TotalAmount'];
        } else if ($row['Payment_Method'] === 'GCash') {
            $gcashSales += $row['Payment_TotalAmount'];
        }
        $totalSales += $row['Payment_TotalAmount'];
    }
    
    $orders[$orderId]['items'][] = [
        'name' => $row['MenuItem_Name'],
        'size' => $row['OrderItem_CupSize'],
        'quantity' => $row['OrderItem_Quantity'],
        'price' => $row['OrderItem_Price']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - SINCO CAFE - <?php echo $dateLabel; ?></title>
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Css-admin/payment-details.css">
    <style>
        .main-content {
            padding: 20px;
            color: #1565c0;
        }
        .filter-controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .date-label {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .orders-container {
            margin-bottom: 20px;
        }
        .order-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            padding: 15px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .order-items {
            margin-left: 20px;
        }
        .order-total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
        .sales-summary {
            background: #fff;
            border-radius: 8px;
            border: 2px solid #1565c0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            padding: 20px;
            margin-top: 30px;
        }
        .sales-summary h3 {
            margin-bottom: 15px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-sales {
            font-size: 1.2em;
            font-weight: bold;
            border-top: 2px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
        }
        .badge-eating-option {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .badge-dine-in {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .badge-take-out {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        .payment-method {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .payment-cash {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .payment-gcash {
            background-color: #bbdefb;
            color: #1565c0;
        }
        .report-header-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header {
            margin-bottom: 20px;
        }
        .report-header img {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .report-title-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-user {
            font-style: italic;
            color: #1976d2;
            margin-top: 10px;
        }
        .date-label {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 20px;
            /* text-align: center; */
        }
        @media print {
            .wrapper { display: block !important; }
            .sidebar, .mobile-menu-toggle, .filter-controls, .print-button { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 20px !important; }
            .report-header-section { margin-bottom: 30px;}
            .report-header { text-align: center; margin-bottom: 20px; }
            .report-header img { 
                max-width: 200px; 
                height: auto; 
                margin: 0 auto;
                padding: 10px !important;
                border-radius: 8px !important;
                filter: invert(1) !important; /* Inverts the logo colors */
            }
            .report-title-section { 
                text-align: center !important;
                margin-bottom: 20px;
            }
            .report-user {
                text-align: center !important;
                margin-top: 10px !important;
            }
            .date-label {
                text-align: center !important;
            }
            .order-card {
                page-break-inside: avoid;
                border: 1px solid #ddd !important;
                margin-bottom: 20px !important;
                box-shadow: none !important;
            }
            .sales-summary {
                border: 2px solid #1565c0 !important;
            }
            body { background: white !important; }
            h2 { color: #1565c0 !important; }
            @page {
                size: A4;
                margin: 2cm;
            }
        }
        .print-button {
            background: #1565c0;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .print-button:hover {
            background: #1976d2;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php 
        require_once 'includes/sidebar.php';
        renderSidebar('history');
        ?>

        <div class="main-content">
            <div class="mobile-menu-toggle d-lg-none">
                <button class="btn btn-dark" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="report-header-section">
                <div class="report-header">
                    <img src="Images/logo/logo.png" alt="SINCO CAFE Logo" style="border-radius: 20px;">
                </div>
                <div class="report-title-section">
                    <h2>Sales Report</h2>
                </div>
                <div class="report-user">
                    Generated by: <?php 
                        if ($staff_info) {
                            echo $staff_info['Staff_FirstName'] . ' ' . 
                                 $staff_info['Staff_MiddleName'] . ' ' . 
                                 $staff_info['Staff_LastName'];
                        } else {
                            echo 'Unknown User';
                        }
                    ?>
                </div>
                
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex gap-3 align-items-center">
                    <form method="GET" class="d-flex gap-3 align-items-center">
                    <select name="period" class="form-select" style="width: auto;" onchange="this.form.submit()">
                        <option value="daily" <?php echo $period === 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo $period === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="monthly" <?php echo $period === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                    </select>
                    <input type="date" name="date" value="<?php echo $date; ?>" class="form-control" style="width: auto;" onchange="this.form.submit()">
                    </form>
                    <input type="text" id="ticketSearch" class="form-control" style="width: auto;" placeholder="Search Ticket #" onkeyup="searchTicket()">
                </div>
                <button onclick="window.print()" class="print-button">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>

            <div class="date-label"><?php echo $dateLabel; ?></div>

            <!-- Orders List -->
            <div class="orders-container">
                <?php foreach ($orders as $orderId => $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <strong>Order #<?php echo $orderId; ?></strong>
                                <span class="ms-2">Ticket #<?php echo $order['ticket']; ?></span>
                                <span class="ms-2 text-muted"><?php echo date('M d, Y h:i A', strtotime($order['datetime'])); ?></span>
                                <span class="ms-2 badge-eating-option <?php echo $order['eating_option'] === 'Dine-in' ? 'badge-dine-in' : 'badge-take-out'; ?>">
                                    <?php echo $order['eating_option']; ?>
                                </span>
                            </div>
                            <div class="payment-info">
                                <span class="payment-method <?php echo $order['payment_method'] === 'Cash' ? 'payment-cash' : 'payment-gcash'; ?>">
                                    <?php echo $order['payment_method']; ?>
                                </span>
                                <?php if ($order['payment_method'] === 'Cash'): ?>
                                    <div class="payment-details">
                                        <span class="payment-amount">Cash: ₱<?php echo number_format($order['cash_paid'], 2); ?></span>
                                        <span class="payment-separator">|</span>
                                        <span class="payment-amount">Change: ₱<?php echo number_format($order['change'], 2); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="payment-details">
                                        Ref #: <span class="reference-number"><?php echo $order['reference_number']; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="d-flex justify-content-between mb-1">
                                    <div>
                                        <?php echo $item['quantity']; ?>x <?php echo $item['name']; ?> 
                                        <?php if ($item['size']): ?>
                                            <small class="text-muted">(<?php echo $item['size']; ?>)</small>
                                        <?php endif; ?>
                                    </div>
                                    <div>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-total text-muted">
                            Sub Total: ₱<?php echo number_format($order['subtotal'], 2); ?>
                        </div>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="order-total text-muted">
                                Discount (<?php echo $order['discount_type']; ?>): 
                                ₱<?php echo number_format($order['discount_amount'], 2); ?>
                            </div>
                        <?php endif; ?>
                        <div class="order-total">
                            Total Amount: ₱<?php echo number_format($order['total'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">No completed orders found for this period.</div>
                <?php endif; ?>
            </div>

            <!-- Sales Summary -->
            <div class="sales-summary">
                <h3>Sales Summary</h3>
                <div class="summary-item">
                    <span>Total Orders:</span>
                    <span><?php echo $totalOrders; ?></span>
                </div>
                <div class="summary-item" style="background-color: #c8e6c9; color: #2e7d32;">
                    <span>Cash Sales:</span>
                    <span>₱<?php echo number_format($cashSales, 2); ?></span>
                </div>
                <div class="summary-item" style="background-color: #bbdefb; color: #1565c0;">
                    <span>GCash Sales:</span>
                    <span>₱<?php echo number_format($gcashSales, 2); ?></span>
                </div>
                <div class="summary-item total-sales">
                    <span>Total Sales:</span>
                    <span>₱<?php echo number_format($totalSales, 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script src="Css-admin/bootstrap.bundle.min.js"></script>
    <script src="Javascript-admin/mobile-menu.js"></script>
    <script>
        function searchTicket() {
            const searchValue = document.getElementById('ticketSearch').value.toLowerCase();
            const orderCards = document.querySelectorAll('.order-card');
            let hasVisibleOrders = false;

            orderCards.forEach(card => {
                const ticketText = card.querySelector('span').textContent.toLowerCase();
                if (ticketText.includes('ticket #' + searchValue)) {
                    card.style.display = '';
                    hasVisibleOrders = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            let noResultsMsg = document.getElementById('noResultsMsg');
            if (!hasVisibleOrders) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMsg';
                    noResultsMsg.className = 'alert alert-info';
                    noResultsMsg.textContent = 'No tickets found matching your search.';
                    document.querySelector('.orders-container').appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = '';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        }
    </script>
</body>
</html>
