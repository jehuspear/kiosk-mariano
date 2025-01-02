<?php
session_start();
include 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Function to validate size name
function validateSizeName($sizeName) {
    return !empty($sizeName) && strlen($sizeName) <= 50;
}

// Function to validate temperature type
function validateTemperatureType($type) {
    $validTypes = ['Hot', 'Iced', 'Normal'];
    return in_array($type, $validTypes);
}

// Function to get default temperature type based on category
function getDefaultTemperatureType($category) {
    $hotCategories = ['Traditional Coffee', 'Coffee'];
    $coldCategories = ['Mocktail', 'Non-Coffee'];
    $normalCategories = ['Pastries', 'Snacks'];

    if (in_array($category, $hotCategories)) return 'Hot';
    if (in_array($category, $coldCategories)) return 'Iced';
    if (in_array($category, $normalCategories)) return 'Normal';
    return 'Normal'; // Default fallback
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch($action) {
        case 'create':
            if (isset($_POST['menuItemId'], $_POST['sizeName'], $_POST['price'], $_POST['temperatureType'], $_POST['stock'])) {
                $menuItemId = intval($_POST['menuItemId']);
                $sizeName = $_POST['sizeName'];
                $price = floatval($_POST['price']);
                $temperatureType = $_POST['temperatureType'];
                $stock = intval($_POST['stock']);

                // Validate size name
                if (!validateSizeName($sizeName)) {
                    $response = ['success' => false, 'message' => 'Invalid size name'];
                    break;
                }

                // Validate temperature type
                if (!validateTemperatureType($temperatureType)) {
                    $response = ['success' => false, 'message' => 'Invalid temperature type'];
                    break;
                }

                // Check if size already exists for this menu item
                $checkSql = "SELECT MenuItemSize_ID FROM menuitem_sizes WHERE MenuItem_ID = ? AND MenuItemSize_SizeName = ?";
                $checkStmt = mysqli_prepare($conn, $checkSql);
                mysqli_stmt_bind_param($checkStmt, "is", $menuItemId, $sizeName);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $response = ['success' => false, 'message' => 'Size already exists for this menu item'];
                    break;
                }

                // Start transaction
                mysqli_begin_transaction($conn);
                try {
                    // Insert new size
                    $sql = "INSERT INTO menuitem_sizes (
                        MenuItem_ID, 
                        MenuItemSize_SizeName, 
                        MenuItemSize_Price, 
                        MenuItemSize_IsHot, 
                        MenuItemSize_Stock,
                        MenuItemSize_Sold
                    ) VALUES (?, ?, ?, ?, ?, 0)";
                    
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "isdsi", 
                        $menuItemId, 
                        $sizeName, 
                        $price, 
                        $temperatureType, 
                        $stock
                    );
                    mysqli_stmt_execute($stmt);

                    // Update total stocks in menuitem table
                    $updateStocksSql = "UPDATE menuitem 
                                      SET MenuItem_TotalStocks = (
                                          SELECT SUM(MenuItemSize_Stock) 
                                          FROM menuitem_sizes 
                                          WHERE MenuItem_ID = ?
                                      ) 
                                      WHERE MenuItem_ID = ?";
                    $updateStocksStmt = mysqli_prepare($conn, $updateStocksSql);
                    mysqli_stmt_bind_param($updateStocksStmt, "ii", $menuItemId, $menuItemId);
                    mysqli_stmt_execute($updateStocksStmt);

                    mysqli_commit($conn);
                    $response = ['success' => true, 'message' => 'Size added successfully'];
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $response = ['success' => false, 'message' => 'Error adding size: ' . $e->getMessage()];
                }
            }
            break;

        case 'update':
            if (isset($_POST['sizeId'], $_POST['sizeName'], $_POST['price'], $_POST['temperatureType'], $_POST['stock'])) {
                $sizeId = intval($_POST['sizeId']);
                $sizeName = $_POST['sizeName'];
                $price = floatval($_POST['price']);
                $temperatureType = $_POST['temperatureType'];
                $stock = intval($_POST['stock']);

                // Validate size name
                if (!validateSizeName($sizeName)) {
                    $response = ['success' => false, 'message' => 'Invalid size name'];
                    break;
                }

                // Validate temperature type
                if (!validateTemperatureType($temperatureType)) {
                    $response = ['success' => false, 'message' => 'Invalid temperature type'];
                    break;
                }

                // Get menu item ID
                $menuItemQuery = "SELECT MenuItem_ID FROM menuitem_sizes WHERE MenuItemSize_ID = ?";
                $menuItemStmt = mysqli_prepare($conn, $menuItemQuery);
                mysqli_stmt_bind_param($menuItemStmt, "i", $sizeId);
                mysqli_stmt_execute($menuItemStmt);
                $menuItemResult = mysqli_stmt_get_result($menuItemStmt);
                $menuItemRow = mysqli_fetch_assoc($menuItemResult);
                $menuItemId = $menuItemRow['MenuItem_ID'];

                // Check if size name already exists for another size of this menu item
                $checkSql = "SELECT MenuItemSize_ID FROM menuitem_sizes 
                            WHERE MenuItem_ID = ? AND MenuItemSize_SizeName = ? AND MenuItemSize_ID != ?";
                $checkStmt = mysqli_prepare($conn, $checkSql);
                mysqli_stmt_bind_param($checkStmt, "isi", $menuItemId, $sizeName, $sizeId);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $response = ['success' => false, 'message' => 'Size name already exists for this menu item'];
                    break;
                }

                // Start transaction
                mysqli_begin_transaction($conn);
                try {
                    // Update size
                    $sql = "UPDATE menuitem_sizes 
                            SET MenuItemSize_SizeName = ?,
                                MenuItemSize_Price = ?, 
                                MenuItemSize_IsHot = ?, 
                                MenuItemSize_Stock = ?
                            WHERE MenuItemSize_ID = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "sdsii", 
                        $sizeName,
                        $price, 
                        $temperatureType, 
                        $stock, 
                        $sizeId
                    );
                    mysqli_stmt_execute($stmt);

                    // Update total stocks in menuitem table
                    $updateStocksSql = "UPDATE menuitem 
                                      SET MenuItem_TotalStocks = (
                                          SELECT SUM(MenuItemSize_Stock) 
                                          FROM menuitem_sizes 
                                          WHERE MenuItem_ID = ?
                                      ) 
                                      WHERE MenuItem_ID = ?";
                    $updateStocksStmt = mysqli_prepare($conn, $updateStocksSql);
                    mysqli_stmt_bind_param($updateStocksStmt, "ii", $menuItemId, $menuItemId);
                    mysqli_stmt_execute($updateStocksStmt);

                    mysqli_commit($conn);
                    $response = ['success' => true, 'message' => 'Size updated successfully'];
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $response = ['success' => false, 'message' => 'Error updating size: ' . $e->getMessage()];
                }
            }
            break;

        case 'delete':
            if (isset($_POST['sizeId'])) {
                $sizeId = intval($_POST['sizeId']);

                // Get menu item ID
                $menuItemQuery = "SELECT MenuItem_ID FROM menuitem_sizes WHERE MenuItemSize_ID = ?";
                $menuItemStmt = mysqli_prepare($conn, $menuItemQuery);
                mysqli_stmt_bind_param($menuItemStmt, "i", $sizeId);
                mysqli_stmt_execute($menuItemStmt);
                $menuItemResult = mysqli_stmt_get_result($menuItemStmt);
                $menuItemRow = mysqli_fetch_assoc($menuItemResult);
                $menuItemId = $menuItemRow['MenuItem_ID'];

                // Start transaction
                mysqli_begin_transaction($conn);
                try {
                    // Delete size
                    $sql = "DELETE FROM menuitem_sizes WHERE MenuItemSize_ID = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $sizeId);
                    mysqli_stmt_execute($stmt);

                    // Update total stocks in menuitem table
                    $updateStocksSql = "UPDATE menuitem 
                                      SET MenuItem_TotalStocks = COALESCE((
                                          SELECT SUM(MenuItemSize_Stock) 
                                          FROM menuitem_sizes 
                                          WHERE MenuItem_ID = ?
                                      ), 0)
                                      WHERE MenuItem_ID = ?";
                    $updateStocksStmt = mysqli_prepare($conn, $updateStocksSql);
                    mysqli_stmt_bind_param($updateStocksStmt, "ii", $menuItemId, $menuItemId);
                    mysqli_stmt_execute($updateStocksStmt);

                    mysqli_commit($conn);
                    $response = ['success' => true, 'message' => 'Size deleted successfully'];
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $response = ['success' => false, 'message' => 'Error deleting size: ' . $e->getMessage()];
                }
            }
            break;
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle GET requests for fetching size details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sizeId'])) {
    $sizeId = intval($_GET['sizeId']);
    
    $sql = "SELECT * FROM menuitem_sizes WHERE MenuItemSize_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $sizeId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($size = mysqli_fetch_assoc($result)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $size]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Size not found']);
    }
    exit();
}
?>
