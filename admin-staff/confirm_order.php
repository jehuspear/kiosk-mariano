<?php
require_once 'database_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    
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
        
        // Update order status to Preparing
        $sql = "UPDATE `order` SET Order_Status = 'Preparing' WHERE Order_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        
        // Update payment status and datetime
        $sql = "UPDATE payment SET 
                Payment_Status = 'Completed',
                Payment_DateTime = NOW()
                WHERE Payment_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $payment_id);
        mysqli_stmt_execute($stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Order and payment status updated successfully']);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    mysqli_close($conn);
}
?>
