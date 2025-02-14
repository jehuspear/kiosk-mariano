<?php
session_start();
require_once 'database_admin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get and validate input
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$order_id || !in_array($action, ['approve', 'decline'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input parameters']);
    exit;
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    // Get order and payment details
    $sql = "SELECT o.Order_ID, o.Payment_ID, o.Order_Status, p.Payment_Status 
            FROM `order` o 
            JOIN payment p ON o.Payment_ID = p.Payment_ID 
            WHERE o.Order_ID = ? FOR UPDATE";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to fetch order details: " . mysqli_error($conn));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    
    if (!$order) {
        throw new Exception("Order not found");
    }
    
    if ($order['Order_Status'] !== 'Preparing') {
        throw new Exception("Invalid order status. Only 'Preparing' orders can be updated.");
    }
    
    // Set new order status
    $new_order_status = $action === 'approve' ? 'ReadyToClaim' : 'Cancelled';
    
    // Update order status
    $sql = "UPDATE `order` SET Order_Status = ? WHERE Order_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_order_status, $order_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update order status: " . mysqli_error($conn));
    }

    
    // Set timezone and get current datetime
    date_default_timezone_set('Asia/Manila');
    $currentDateTime = date('Y-m-d H:i:s');

    // Update order completed time when approving
    if ($action === 'approve') {
        $sql = "UPDATE `order` SET Order_CompletedTime = ? WHERE Order_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $currentDateTime, $order_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update order completed time: " . mysqli_error($conn));
        }
    }
    
    // Log the action
    $staff_id = $_SESSION['user_id'] ?? 1;
    $log_action = $action === 'approve' ? 'Order marked as ready' : 'Order cancelled';
    $log_details = "Order #$order_id status changed to $new_order_status";
    
    $sql = "INSERT INTO logs (Staff_ID, Log_DateTime, Log_Action, Log_Details) 
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $staff_id, $currentDateTime, $log_action, $log_details);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to log action: " . mysqli_error($conn));
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode([
        'success' => true,
        'message' => $action === 'approve' 
            ? 'Order marked as ready to claim' 
            : 'Order has been cancelled',
        'new_status' => $new_order_status
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    mysqli_close($conn);
}
?>
