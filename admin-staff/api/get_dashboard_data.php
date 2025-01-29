<?php
require_once '../database_admin.php';

function getDailyCustomerCount($date) {
    global $conn;
    $sql = "SELECT DATE(Order_DateTime) as date, COUNT(DISTINCT Order_ID) as count 
            FROM `order` 
            WHERE DATE(Order_DateTime) = ?
            GROUP BY DATE(Order_DateTime)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'] ?? 0;
}

function getWeeklyCustomerCount($startDate) {
    global $conn;
    $sql = "SELECT DATE(Order_DateTime) as date, COUNT(DISTINCT Order_ID) as count 
            FROM `order` 
            WHERE Order_DateTime >= ? AND Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
            GROUP BY DATE(Order_DateTime)
            ORDER BY date";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $startDate);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getMonthlyCustomerCount($startDate) {
    global $conn;
    $sql = "SELECT DATE(Order_DateTime) as date, COUNT(DISTINCT Order_ID) as count 
            FROM `order` 
            WHERE DATE_FORMAT(Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
            GROUP BY DATE(Order_DateTime)
            ORDER BY date";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $startDate);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getProductsSold($period, $date) {
    global $conn;
    $sql = "";
    $params = [];
    $types = "";
    
    switch($period) {
        case 'daily':
            $sql = "SELECT DATE(o.Order_DateTime) as date, SUM(oi.OrderItem_Quantity) as count 
                    FROM `order` o 
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                    WHERE DATE(o.Order_DateTime) = ?
                    GROUP BY DATE(o.Order_DateTime)";
            $params = [$date];
            $types = "s";
            break;
            
        case 'weekly':
            $sql = "SELECT DATE(o.Order_DateTime) as date, SUM(oi.OrderItem_Quantity) as count 
                    FROM `order` o 
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                    WHERE o.Order_DateTime >= ? AND o.Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
                    GROUP BY DATE(o.Order_DateTime)
                    ORDER BY date";
            $params = [$date, $date];
            $types = "ss";
            break;
            
        case 'monthly':
            $sql = "SELECT DATE(o.Order_DateTime) as date, SUM(oi.OrderItem_Quantity) as count 
                    FROM `order` o 
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID 
                    WHERE DATE_FORMAT(o.Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                    GROUP BY DATE(o.Order_DateTime)
                    ORDER BY date";
            $params = [$date];
            $types = "s";
            break;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getRevenue($period, $date) {
    global $conn;
    $sql = "";
    $params = [];
    $types = "";
    
    switch($period) {
        case 'daily':
            $sql = "SELECT DATE(Order_DateTime) as date, 
                    SUM(CASE WHEN Payment_Method = 'Cash' THEN Order_TotalAmount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN Payment_Method = 'GCash' THEN Order_TotalAmount ELSE 0 END) as gcash_total,
                    SUM(Order_TotalAmount) as total
                    FROM `order` 
                    WHERE DATE(Order_DateTime) = ?
                    GROUP BY DATE(Order_DateTime)";
            $params = [$date];
            $types = "s";
            break;
            
        case 'weekly':
            $sql = "SELECT DATE(Order_DateTime) as date,
                    SUM(CASE WHEN Payment_Method = 'Cash' THEN Order_TotalAmount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN Payment_Method = 'GCash' THEN Order_TotalAmount ELSE 0 END) as gcash_total,
                    SUM(Order_TotalAmount) as total
                    FROM `order`
                    WHERE Order_DateTime >= ? AND Order_DateTime < DATE_ADD(?, INTERVAL 7 DAY)
                    GROUP BY DATE(Order_DateTime)
                    ORDER BY date";
            $params = [$date, $date];
            $types = "ss";
            break;
            
        case 'monthly':
            $sql = "SELECT DATE(Order_DateTime) as date,
                    SUM(CASE WHEN Payment_Method = 'Cash' THEN Order_TotalAmount ELSE 0 END) as cash_total,
                    SUM(CASE WHEN Payment_Method = 'GCash' THEN Order_TotalAmount ELSE 0 END) as gcash_total,
                    SUM(Order_TotalAmount) as total
                    FROM `order`
                    WHERE DATE_FORMAT(Order_DateTime, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                    GROUP BY DATE(Order_DateTime)
                    ORDER BY date";
            $params = [$date];
            $types = "s";
            break;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTopProducts() {
    global $conn;
    $sql = "SELECT m.MenuItem_Name, SUM(oi.OrderItem_Quantity) as total_sold
            FROM orderitem oi
            JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
            GROUP BY m.MenuItem_ID, m.MenuItem_Name
            ORDER BY total_sold DESC
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
                $response = ['count' => getDailyCustomerCount($date)];
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
