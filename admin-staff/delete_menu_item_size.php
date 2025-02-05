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

// Get and validate size ID
$sizeId = isset($_POST['size_id']) ? intval($_POST['size_id']) : 0;

if ($sizeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid size ID']);
    exit();
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Get the MenuItem_ID before deleting the size
    $getMenuItemSql = "SELECT MenuItem_ID FROM menuitem_sizes WHERE MenuItemSize_ID = ?";
    $menuItemStmt = mysqli_prepare($conn, $getMenuItemSql);
    if (!$menuItemStmt) {
        throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($menuItemStmt, "i", $sizeId);
    
    if (!mysqli_stmt_execute($menuItemStmt)) {
        throw new Exception("Failed to get menu item ID: " . mysqli_stmt_error($menuItemStmt));
    }

    $menuItemResult = mysqli_stmt_get_result($menuItemStmt);
    $menuItemRow = mysqli_fetch_assoc($menuItemResult);
    
    if (!$menuItemRow) {
        throw new Exception("Size not found");
    }

    $menuItemId = $menuItemRow['MenuItem_ID'];

    // Delete the size
    $deleteSql = "DELETE FROM menuitem_sizes WHERE MenuItemSize_ID = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    if (!$deleteStmt) {
        throw new Exception("Failed to prepare delete statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($deleteStmt, "i", $sizeId);
    
    if (!mysqli_stmt_execute($deleteStmt)) {
        throw new Exception("Failed to delete size: " . mysqli_stmt_error($deleteStmt));
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
    
    echo json_encode(['success' => true, 'message' => 'Size deleted successfully']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
