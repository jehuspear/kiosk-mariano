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
$menuItemId = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;
$sizeName = isset($_POST['size_name']) ? trim($_POST['size_name']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$temperatureType = isset($_POST['temperature_type']) ? trim($_POST['temperature_type']) : '';
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

// Validate inputs
if ($menuItemId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid menu item ID']);
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

    // Check if size name already exists for this menu item
    $checkSql = "SELECT COUNT(*) as count FROM menuitem_sizes WHERE MenuItem_ID = ? AND MenuItemSize_SizeName = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    if (!$checkStmt) {
        throw new Exception("Failed to prepare check statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($checkStmt, "is", $menuItemId, $sizeName);
    if (!mysqli_stmt_execute($checkStmt)) {
        throw new Exception("Failed to check size existence: " . mysqli_stmt_error($checkStmt));
    }

    $checkResult = mysqli_stmt_get_result($checkStmt);
    $checkRow = mysqli_fetch_assoc($checkResult);
    if ($checkRow['count'] > 0) {
        throw new Exception("Size name already exists for this menu item");
    }

    // Insert new size
    $sql = "INSERT INTO menuitem_sizes 
            (MenuItem_ID, MenuItemSize_SizeName, MenuItemSize_Price, MenuItemSize_IsHot, MenuItemSize_Stock, MenuItemSize_Sold) 
            VALUES (?, ?, ?, ?, ?, 0)";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "isdsi", $menuItemId, $sizeName, $price, $temperatureType, $stock);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create size: " . mysqli_stmt_error($stmt));
    }

    // Update total stocks in menuitem table
    $updateTotalStocksSql = "UPDATE menuitem m 
                            SET m.MenuItem_TotalStocks = (
                                SELECT COALESCE(SUM(ms.MenuItemSize_Stock), 0)
                                FROM menuitem_sizes ms 
                                WHERE ms.MenuItem_ID = m.MenuItem_ID
                            )
                            WHERE m.MenuItem_ID = ?";
    
    $totalStocksStmt = mysqli_prepare($conn, $updateTotalStocksSql);
    if (!$totalStocksStmt) {
        throw new Exception("Failed to prepare total stocks statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($totalStocksStmt, "i", $menuItemId);
    
    if (!mysqli_stmt_execute($totalStocksStmt)) {
        throw new Exception("Failed to update total stocks: " . mysqli_stmt_error($totalStocksStmt));
    }

    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode(['success' => true, 'message' => 'Size created successfully']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
