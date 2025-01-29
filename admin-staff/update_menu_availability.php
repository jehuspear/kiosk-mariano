<?php
session_start();
require_once 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if required parameters are present
if (!isset($_POST['menuItemId']) || !isset($_POST['availability'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$menuItemId = intval($_POST['menuItemId']);
$availability = $_POST['availability'] === 'true' ? 'Available' : 'Unavailable';

// Update the menu item availability
$sql = "UPDATE menuitem SET MenuItem_Availability = ? WHERE MenuItem_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $availability, $menuItemId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Menu item availability updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update menu item availability']);
}

$stmt->close();
$conn->close();
?>
