<?php
require_once '../database_admin.php';

function getDailyCustomerCount($date) {
    global $conn;
    
    // Get hourly data
    $sql = "SELECT Order_DateTime as datetime, COUNT(DISTINCT Order_ID) as count 
            FROM `order` 
            WHERE DATE(Order_DateTime) = ?
            AND Order_Status = 'Completed'
            GROUP BY HOUR(Order_DateTime)
            ORDER BY Order_DateTime";
    
    // Get total for the day
    $totalSql = "SELECT COUNT(DISTINCT Order_ID) as total
                 FROM `order`
                 WHERE DATE(Order_DateTime) = ?
                 AND Order_Status = 'Completed'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param("s", $date);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result()->fetch_assoc();
    
    return [
        'data' => $result,
        'total' => $totalResult['total'] ?? 0
    ];
}

function getWeeklyCustomerCount($startDate) {
    global $conn;
    
    // Get daily data
    $sql = "SELECT DATE(Order_DateTime) as date, COUNT(DISTINCT Order_ID) as count 
            FROM `order` 
            WHERE Order_DateTime >= ? AND Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
            AND Order_Status = 'Completed'
            GROUP BY DATE(Order_DateTime)
            ORDER BY date";
    
    // Get total for the week
    $totalSql = "SELECT COUNT(DISTINCT Order_ID) as total
                 FROM `order`
                 WHERE Order_DateTime >= ? AND Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
                 AND Order_Status = 'Completed'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $startDate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param("ss", $startDate, $startDate);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result()->fetch_assoc();
    
    return [
        'data' => $result,
        'total' => $totalResult['total'] ?? 0
    ];
}

function getMonthlyCustomerCount($startDate) {
    global $conn;
    
    // Get daily data
    $sql = "SELECT DATE(Order_DateTime) as date, COUNT(DISTINCT Order_ID) as count 
            FROM `order` 
            WHERE DATE_FORMAT(Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
            AND Order_Status = 'Completed'
            GROUP BY DATE(Order_DateTime)
            ORDER BY date";
    
    // Get total for the month
    $totalSql = "SELECT COUNT(DISTINCT Order_ID) as total
                 FROM `order`
                 WHERE DATE_FORMAT(Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                 AND Order_Status = 'Completed'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $startDate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param("s", $startDate);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result()->fetch_assoc();
    
    return [
        'data' => $result,
        'total' => $totalResult['total'] ?? 0
    ];
}

function getProductsSold($period, $date) {
    global $conn;
    $sql = "";
    $totalSql = "";
    $params = [];
    $types = "";
    
    switch($period) {
        case 'daily':
            $sql = "SELECT 
                    Order_DateTime as datetime,
                    SUM(oi.OrderItem_Quantity) as count
                    FROM `order` o 
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                    WHERE DATE(o.Order_DateTime) = ?
                    AND o.Order_Status = 'Completed'
                    GROUP BY HOUR(Order_DateTime)
                    ORDER BY Order_DateTime";
            $totalSql = "SELECT 
                        SUM(oi.OrderItem_Quantity) as total
                        FROM `order` o 
                        JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                        WHERE DATE(o.Order_DateTime) = ?
                        AND o.Order_Status = 'Completed'";
            $params = [$date];
            $types = "s";
            break;
            
        case 'weekly':
            $sql = "SELECT 
                    DATE(o.Order_DateTime) as date,
                    SUM(oi.OrderItem_Quantity) as count
                    FROM `order` o 
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                    WHERE o.Order_DateTime >= ? AND o.Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
                    AND o.Order_Status = 'Completed'
                    GROUP BY DATE(o.Order_DateTime)
                    ORDER BY date";
            $totalSql = "SELECT 
                        SUM(oi.OrderItem_Quantity) as total
                        FROM `order` o 
                        JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                        WHERE o.Order_DateTime >= ? AND o.Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
                        AND o.Order_Status = 'Completed'";
            $params = [$date, $date];
            $types = "ss";
            break;
            
        case 'monthly':
            $sql = "SELECT 
                    DATE(o.Order_DateTime) as date,
                    SUM(oi.OrderItem_Quantity) as count
                    FROM `order` o 
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                    WHERE DATE_FORMAT(o.Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                    AND o.Order_Status = 'Completed'
                    GROUP BY DATE(o.Order_DateTime)
                    ORDER BY date";
            $totalSql = "SELECT 
                        SUM(oi.OrderItem_Quantity) as total
                        FROM `order` o 
                        JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                        WHERE DATE_FORMAT(o.Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                        AND o.Order_Status = 'Completed'";
            $params = [$date];
            $types = "s";
            break;
    }
    
    // Get daily data
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get total
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param($types, ...$params);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result()->fetch_assoc();
    
    return [
        'data' => $result,
        'total' => $totalResult['total'] ?? 0
    ];
}

function getRevenue($period, $date) {
    global $conn;
    $sql = "";
    $params = [];
    $types = "";
    
    switch($period) {
        case 'daily':
            $sql = "SELECT 
                    Order_DateTime as datetime,
                    SUM(CASE WHEN Payment_Method = 'Cash' THEN Order_TotalAmount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN Payment_Method = 'GCash' THEN Order_TotalAmount ELSE 0 END) as gcash_total,
                    SUM(Order_TotalAmount) as total
                    FROM `order` 
                    WHERE DATE(Order_DateTime) = ?
                    AND Order_Status = 'Completed'
                    GROUP BY HOUR(Order_DateTime)
                    ORDER BY Order_DateTime";
            $params = [$date];
            $types = "s";
            break;
            
        case 'weekly':
            $sql = "SELECT 
                    DATE(Order_DateTime) as date,
                    SUM(CASE WHEN Payment_Method = 'Cash' THEN Order_TotalAmount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN Payment_Method = 'GCash' THEN Order_TotalAmount ELSE 0 END) as gcash_total,
                    SUM(Order_TotalAmount) as total
                    FROM `order`
                    WHERE Order_DateTime >= ? AND Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
                    AND Order_Status = 'Completed'
                    GROUP BY DATE(Order_DateTime)
                    ORDER BY date";
            $params = [$date, $date];
            $types = "ss";
            break;
            
        case 'monthly':
            $sql = "SELECT 
                    DATE(Order_DateTime) as date,
                    SUM(CASE WHEN Payment_Method = 'Cash' THEN Order_TotalAmount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN Payment_Method = 'GCash' THEN Order_TotalAmount ELSE 0 END) as gcash_total,
                    SUM(Order_TotalAmount) as total
                    FROM `order`
                    WHERE DATE_FORMAT(Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                    AND Order_Status = 'Completed'
                    GROUP BY DATE(Order_DateTime)
                    ORDER BY date";
            $params = [$date];
            $types = "s";
            break;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Calculate totals
    $totalCash = 0;
    $totalGcash = 0;
    $totalAmount = 0;
    
    foreach ($result as $row) {
        $totalCash += $row['cash_total'];
        $totalGcash += $row['gcash_total'];
        $totalAmount += $row['total'];
    }
    
    return [
        'data' => $result,
        'totals' => [
            'cash_total' => $totalCash,
            'gcash_total' => $totalGcash,
            'total' => $totalAmount
        ]
    ];
}

function getTopProducts() {
    global $conn;
    $sql = "SELECT MenuItem_Name, MenuItem_Image, MenuItem_TotalSold as total_sold
            FROM menuitem
            ORDER BY MenuItem_TotalSold DESC
            LIMIT 5";
    
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle API requests
$action = $_GET['action'] ?? '';
$period = $_GET['period'] ?? 'daily';
$date = $_GET['date'] ?? date('Y-m-d');

$response = [];

switch($action) {
    case 'customers':
        switch($period) {
            case 'daily':
                $response = getDailyCustomerCount($date);
                break;
            case 'weekly':
                $response = getWeeklyCustomerCount($date);
                break;
            case 'monthly':
                $response = getMonthlyCustomerCount($date);
                break;
        }
        break;
        
    case 'products':
        $response = getProductsSold($period, $date);
        break;
        
    case 'revenue':
        $response = getRevenue($period, $date);
        break;
        
    case 'top_products':
        $response = getTopProducts();
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
