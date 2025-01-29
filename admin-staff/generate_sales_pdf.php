<?php
require_once('database_admin.php');

// Get parameters
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
               p.Payment_DiscountType, p.Payment_DiscountAmount
        FROM `order` o
        LEFT JOIN orderitem oi ON o.Order_ID = oi.Order_ID
        LEFT JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
        LEFT JOIN payment p ON o.Payment_ID = p.Payment_ID
        WHERE o.Order_Status = 'Completed'
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
            'total' => $row['Order_TotalAmount'],
            'discount_type' => $row['Payment_DiscountType'],
            'discount_amount' => $row['Payment_DiscountAmount'],
            'items' => []
        ];
        
        // Update totals
        $totalOrders++;
        if ($row['Payment_Method'] === 'Cash') {
            $cashSales += $row['Order_TotalAmount'];
        } else if ($row['Payment_Method'] === 'GCash') {
            $gcashSales += $row['Order_TotalAmount'];
        }
        $totalSales += $row['Order_TotalAmount'];
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
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report - <?php echo $dateLabel; ?></title>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px; font-size: 12px; line-height: 1.4;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="color: #1565c0; text-align: center; margin: 0 0 10px 0;">SINCO CAFE - Sales Report</h1>
        <h2 style="color: #1565c0; text-align: center; margin: 0 0 20px 0; font-size: 18px;"><?php echo $dateLabel; ?></h2>

        <?php foreach ($orders as $orderId => $order): ?>
        <div style="border: 1px solid #ddd; margin-bottom: 20px; padding: 15px;">
            <div style="background: #f8f9fa; margin: -15px -15px 15px -15px; padding: 10px; border-bottom: 1px solid #eee;">
                <strong>Order #<?php echo $orderId; ?> - Ticket #<?php echo $order['ticket']; ?></strong>
                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; margin-left: 8px; background: <?php echo $order['eating_option'] === 'Dine-in' ? '#e3f2fd' : '#f3e5f5'; ?>; color: <?php echo $order['eating_option'] === 'Dine-in' ? '#1976d2' : '#7b1fa2'; ?>;">
                    <?php echo $order['eating_option']; ?>
                </span>
                <span style="float: right; display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; background: <?php echo $order['payment_method'] === 'Cash' ? '#c8e6c9' : '#bbdefb'; ?>; color: <?php echo $order['payment_method'] === 'Cash' ? '#2e7d32' : '#1565c0'; ?>;">
                    <?php echo $order['payment_method']; ?>
                </span>
                <div style="clear: both; color: #666; font-size: 0.9em; margin-top: 5px;">
                    <?php echo date('M d, Y h:i A', strtotime($order['datetime'])); ?>
                </div>
            </div>
            
            <div style="margin-left: 15px;">
                <?php foreach ($order['items'] as $item): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>
                        <?php echo $item['quantity']; ?>x <?php echo $item['name']; ?>
                        <?php if ($item['size']): ?>(<?php echo $item['size']; ?>)<?php endif; ?>
                    </span>
                    <span>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
                <?php endforeach; ?>
                
                <?php if ($order['discount_amount'] > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #666;">
                    <span>Discount (<?php echo $order['discount_type']; ?>)</span>
                    <span>-₱<?php echo number_format($order['discount_amount'], 2); ?></span>
                </div>
                <?php endif; ?>
                
                <div style="text-align: right; font-weight: bold; margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                    Total: ₱<?php echo number_format($order['total'], 2); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border: 1px solid #ddd;">
            <h3 style="color: #1565c0; margin: 0 0 15px 0;">Sales Summary</h3>
            <p><strong>Total Orders:</strong> <?php echo $totalOrders; ?></p>
            <p><strong>Cash Sales:</strong> ₱<?php echo number_format($cashSales, 2); ?></p>
            <p><strong>GCash Sales:</strong> ₱<?php echo number_format($gcashSales, 2); ?></p>
            <p style="font-size: 1.2em; margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd;">
                <strong>Total Sales:</strong> ₱<?php echo number_format($totalSales, 2); ?>
            </p>
        </div>
    </div>

    <!-- The Printable Function -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
