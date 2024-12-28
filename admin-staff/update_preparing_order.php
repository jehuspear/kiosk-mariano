<?php
require_once 'database_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Get the Payment_ID from the order
        $sql = "SELECT Payment_ID FROM `order` WHERE Order_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $payment_id = $row['Payment_ID'];
        
        // Set new status based on action
        $new_status = $action === 'approve' ? 'ReadyToClaim' : 'Cancelled';
        $payment_status = $action === 'approve' ? 'Completed' : 'Cancelled';
        
        // Update order status
        $sql = "UPDATE `order` SET Order_Status = ? WHERE Order_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        mysqli_stmt_execute($stmt);
        
        // Update payment status if action is decline
        if ($action === 'decline') {
            $sql = "UPDATE payment SET 
                    Payment_Status = ?,
                    Payment_DateTime = NOW()
                    WHERE Payment_ID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $payment_status, $payment_id);
            mysqli_stmt_execute($stmt);
        }
        
        // Log the action
        $log_action = $action === 'approve' ? 'Order marked as ready' : 'Order cancelled';
        $sql = "INSERT INTO logs (Staff_ID, Log_DateTime, Log_Action, Log_Details) 
                VALUES (?, NOW(), ?, ?)";
        $staff_id = $_SESSION['user_id'] ?? 1; // Use session staff ID if available
        $log_details = "Order #$order_id status changed to $new_status";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $staff_id, $log_action, $log_details);
        mysqli_stmt_execute($stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
