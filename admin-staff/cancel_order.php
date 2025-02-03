<?php
session_start();
require_once 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

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
        
        // Update order status to Cancelled
        $sql = "UPDATE `order` SET Order_Status = 'Cancelled' WHERE Order_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        
        // Update payment status to Cancelled
        $sql = "UPDATE payment SET Payment_Status = 'Cancelled' WHERE Payment_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $payment_id);
        mysqli_stmt_execute($stmt);

        // Log the cancellation
        $staff_id = $_SESSION['user_id'];
        $sql = "INSERT INTO logs (Staff_ID, Log_DateTime, Log_Action, Log_Details) 
                VALUES (?, NOW(), 'Order cancelled', CONCAT('Order #', ?, ' status changed to Cancelled'))";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $staff_id, $order_id);
        mysqli_stmt_execute($stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    mysqli_close($conn);
}
?>
