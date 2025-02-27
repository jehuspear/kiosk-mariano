<?php
// Include database connection
require_once '../database_admin.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Execute the query
    $result = mysqli_query($conn, "SELECT * FROM category ORDER BY Category_ID ASC");
    
    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }
    
    // Fetch all categories as an associative array
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    // Return the categories as JSON
    echo json_encode($categories);
} catch (Exception $e) {
    // Return error message
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
