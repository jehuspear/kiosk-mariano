<?php
// Include database connection
require_once '../database_admin.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get category ID
$categoryId = $_POST['categoryId'] ?? '';

// Validate input
if (empty($categoryId) || !is_numeric($categoryId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
    exit;
}

try {
    // Check if category exists
    $checkQuery = "SELECT COUNT(*) as count FROM category WHERE Category_ID = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "i", $categoryId);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    mysqli_stmt_close($checkStmt);
    
    // Check if the category is being used by any menu items
    $menuItemCheckQuery = "SELECT COUNT(*) as count FROM menuitem WHERE Category_ID = ?";
    $menuItemCheckStmt = mysqli_prepare($conn, $menuItemCheckQuery);
    mysqli_stmt_bind_param($menuItemCheckStmt, "i", $categoryId);
    mysqli_stmt_execute($menuItemCheckStmt);
    $result = mysqli_stmt_get_result($menuItemCheckStmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete this category because it is being used by menu items']);
        mysqli_stmt_close($menuItemCheckStmt);
        exit;
    }
    mysqli_stmt_close($menuItemCheckStmt);
    
    // Prepare and execute the delete query
    $deleteQuery = "DELETE FROM category WHERE Category_ID = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($deleteStmt, "i", $categoryId);
    $result = mysqli_stmt_execute($deleteStmt);
    
    // Check if the delete was successful
    if ($result && mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
    }
    mysqli_stmt_close($deleteStmt);
} catch (Exception $e) {
    // Return error message
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
