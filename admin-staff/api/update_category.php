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

// Get form data
$categoryId = $_POST['categoryId'] ?? '';
$categoryName = trim($_POST['categoryName'] ?? '');
$categoryDescription = trim($_POST['categoryDescription'] ?? '');

// Validate input
if (empty($categoryId) || !is_numeric($categoryId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
    exit;
}

if (empty($categoryName)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
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
    
    // Check if the new name already exists for a different category
    $nameCheckQuery = "SELECT COUNT(*) as count FROM category WHERE Category_Name = ? AND Category_ID != ?";
    $nameCheckStmt = mysqli_prepare($conn, $nameCheckQuery);
    mysqli_stmt_bind_param($nameCheckStmt, "si", $categoryName, $categoryId);
    mysqli_stmt_execute($nameCheckStmt);
    $result = mysqli_stmt_get_result($nameCheckStmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'A category with this name already exists']);
        mysqli_stmt_close($nameCheckStmt);
        exit;
    }
    mysqli_stmt_close($nameCheckStmt);
    
    // Prepare and execute the update query
    $updateQuery = "UPDATE category SET Category_Name = ?, Category_Description = ? WHERE Category_ID = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "ssi", $categoryName, $categoryDescription, $categoryId);
    $result = mysqli_stmt_execute($updateStmt);
    
    // Check if the update was successful
    if ($result && mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
    } else {
        echo json_encode(['success' => true, 'message' => 'No changes were made']);
    }
    mysqli_stmt_close($updateStmt);
} catch (Exception $e) {
    // Return error message
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
