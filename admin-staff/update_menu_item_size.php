<?php
session_start();
require_once 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get and validate input
$sizeId = isset($_POST['size_id']) ? intval($_POST['size_id']) : 0;
$sizeName = isset($_POST['size_name']) ? trim($_POST['size_name']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$temperatureType = isset($_POST['temperature_type']) ? trim($_POST['temperature_type']) : '';
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

// Validate inputs
if ($sizeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid size ID']);
    exit();
}

if (empty($sizeName)) {
    echo json_encode(['success' => false, 'message' => 'Size name is required']);
    exit();
}

if ($price < 0) {
    echo json_encode(['success' => false, 'message' => 'Price cannot be negative']);
    exit();
}

if (!in_array($temperatureType, ['Hot', 'Iced', 'Normal'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid temperature type']);
    exit();
}

if ($stock < 0) {
    echo json_encode(['success' => false, 'message' => 'Stock cannot be negative']);
    exit();
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Update the menu item size
    $sql = "UPDATE menuitem_sizes SET 
            MenuItemSize_SizeName = ?,
            MenuItemSize_Price = ?,
            MenuItemSize_IsHot = ?,
            MenuItemSize_Stock = ?
            WHERE MenuItemSize_ID = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sdsii", $sizeName, $price, $temperatureType, $stock, $sizeId);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update size: " . mysqli_stmt_error($stmt));
    }

    // Update total stocks in menuitem table
    $updateTotalStocksSql = "UPDATE menuitem m 
                            SET m.MenuItem_TotalStocks = (
                                SELECT SUM(ms.MenuItemSize_Stock) 
                                FROM menuitem_sizes ms 
                                WHERE ms.MenuItem_ID = m.MenuItem_ID
                            )
                            WHERE m.MenuItem_ID = (
                                SELECT MenuItem_ID 
                                FROM menuitem_sizes 
                                WHERE MenuItemSize_ID = ?
                            )";
    
    $totalStocksStmt = mysqli_prepare($conn, $updateTotalStocksSql);
    if (!$totalStocksStmt) {
        throw new Exception("Failed to prepare total stocks statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($totalStocksStmt, "i", $sizeId);
    
    if (!mysqli_stmt_execute($totalStocksStmt)) {
        throw new Exception("Failed to update total stocks: " . mysqli_stmt_error($totalStocksStmt));
    }

    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode(['success' => true, 'message' => 'Size updated successfully']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
