<?php
require_once 'database_admin.php';

header('Content-Type: application/json');

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get today's date range in Manila time
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

$sql = "SELECT COUNT(*) as count 
        FROM `order` 
        WHERE Order_Status = 'Preparing'
        AND Order_DateTime BETWEEN ? AND ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $today_start, $today_end);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'count' => $row['count']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => mysqli_error($conn)
    ]);
}
?>
