<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database_admin.php';

// Get the selected date from the request, default to today if not provided
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Validate date format
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $selectedDate)) {
    echo json_encode([
        'success' => false,
        'message' => "Invalid date format"
    ]);
    exit;
}

function calculateTimeDifference($orderDateTime, $completedTime) {
    $orderTime = new DateTime($orderDateTime);
    $completeTime = $completedTime ? new DateTime($completedTime) : new DateTime();
    $diff = $completeTime->diff($orderTime);
    
    // Convert hours to minutes and add to total minutes
    $totalMinutes = ($diff->h * 60) + $diff->i;
    
    return sprintf("%02d:%02d", $totalMinutes, $diff->s);
}

// Debug database connection
if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => "Database connection failed"
    ]);
    exit;
}

// Prepare the SQL query with date filtering
$sql = "SELECT 
        o.Order_ID,
        o.Order_TicketNumber,
        o.Order_EatingOption,
        o.Order_DateTime,
        o.Order_CompletedTime,
        o.Order_TotalAmount,
        o.Payment_Method,
        p.Payment_DiscountAmount,
        p.Payment_TotalAmount,
        GROUP_CONCAT(DISTINCT m.MenuItem_ID) as MenuItemIDs,
        GROUP_CONCAT(CONCAT(
            oi.OrderItem_Quantity, ' x ',
            '[', COALESCE(ms.MenuItemSize_IsHot, 'Normal'), '] ',
            m.MenuItem_Name, ' ', '(', oi.OrderItem_CupSize, ')'
        ) SEPARATOR '<br>') as OrderItems
        FROM `order` o
        LEFT JOIN payment p ON o.Payment_ID = p.Payment_ID
        LEFT JOIN orderitem oi ON o.Order_ID = oi.Order_ID
        LEFT JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
        LEFT JOIN menuitem_sizes ms ON ms.MenuItemSize_ID = oi.MenuItemSize_ID AND oi.OrderItem_CupSize = ms.MenuItemSize_SizeName
        WHERE o.Order_Status IN ('ReadyToClaim', 'Completed')
        AND DATE(o.Order_DateTime) = ?
        GROUP BY o.Order_ID
        ORDER BY o.Order_DateTime DESC";

// Use prepared statement to prevent SQL injection
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $selectedDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    error_log("Query error: " . mysqli_error($conn));
    echo json_encode([
        'success' => false,
        'message' => "Query error: " . mysqli_error($conn)
    ]);
    exit;
}

$orders = [];
$totalCashSales = 0;
$totalGcashSales = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $timeDiff = calculateTimeDifference($row['Order_DateTime'], $row['Order_CompletedTime']);
    $discountPercentage = $row['Payment_DiscountAmount'] > 0 ? 
        ($row['Payment_DiscountAmount'] / $row['Order_TotalAmount']) * 100 : 0;

    $order = [
        'Order_ID' => $row['Order_ID'],
        'Order_TicketNumber' => $row['Order_TicketNumber'],
        'Order_EatingOption' => $row['Order_EatingOption'],
        'MenuItemIDs' => $row['MenuItemIDs'],
        'OrderItems' => $row['OrderItems'],
        'Payment_Method' => $row['Payment_Method'],
        'TimeDifference' => $timeDiff,
        'Cost' => number_format($row['Order_TotalAmount'], 2),
        'Discount' => number_format($discountPercentage, 0),
        'Total' => number_format($row['Payment_TotalAmount'], 2)
    ];
    
    $orders[] = $order;
    
    if($row['Payment_Method'] == 'Cash') {
        $totalCashSales += $row['Payment_TotalAmount'];
    } else if($row['Payment_Method'] == 'GCash') {
        $totalGcashSales += $row['Payment_TotalAmount'];
    }
}

$response = [
    'success' => true,
    'orders' => $orders,
    'totalOrders' => count($orders),
    'totalCashSales' => $totalCashSales,
    'totalGcashSales' => $totalGcashSales,
    'totalSales' => $totalCashSales + $totalGcashSales
];

error_log("Query executed successfully. Found " . count($orders) . " orders");
error_log("Total Cash Sales: " . $totalCashSales);
error_log("Total GCash Sales: " . $totalGcashSales);

echo json_encode($response);

mysqli_close($conn);
?>
