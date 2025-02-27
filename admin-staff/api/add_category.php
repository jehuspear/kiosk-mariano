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
$categoryName = trim($_POST['categoryName'] ?? '');
$categoryDescription = trim($_POST['categoryDescription'] ?? '');

// Validate input
if (empty($categoryName)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

try {
    // Check if category name already exists
    $checkQuery = "SELECT COUNT(*) as count FROM category WHERE Category_Name = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "s", $categoryName);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'A category with this name already exists']);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    mysqli_stmt_close($checkStmt);
    
    // Prepare and execute the insert query
    $insertQuery = "INSERT INTO category (Category_Name, Category_Description) VALUES (?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($insertStmt, "ss", $categoryName, $categoryDescription);
    $result = mysqli_stmt_execute($insertStmt);
    
    // Check if the insert was successful
    if ($result && mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add category']);
    }
    mysqli_stmt_close($insertStmt);
} catch (Exception $e) {
    // Return error message
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
