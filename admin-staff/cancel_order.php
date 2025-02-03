<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display in response
ini_set('log_errors', 1);

header('Content-Type: application/json'); // Set content type before any output

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not logged in'
    ]);
    exit();
}

// Get staff ID early
$staff_id = $_SESSION['user_id'];

// Set flags for database connection
define('ALLOW_DIRECT_ACCESS', true);

try {
    require_once 'database_admin.php';

    // Verify database connection
    if (!isset($conn) || !mysqli_ping($conn)) {
        throw new Exception("Database connection lost");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Debug logging
        error_log("Received POST data: " . print_r($_POST, true));

        // Validate order_id
        if (!isset($_POST['order_id'])) {
            throw new Exception('Order ID is required');
        }

        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
        if ($order_id === false || $order_id <= 0) {
            throw new Exception('Invalid order ID');
        }

        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Get order and payment details with lock
            $sql = "SELECT o.Order_Status, o.Payment_ID, p.Payment_Status 
                   FROM `order` o 
                   JOIN payment p ON o.Payment_ID = p.Payment_ID 
                   WHERE o.Order_ID = ? 
                   AND o.Order_Status = 'Pending'
                   FOR UPDATE";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "i", $order_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to execute statement: " . mysqli_error($conn));
            }

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if (!$row) {
                throw new Exception("Order not found or not in Pending status");
            }

            // Update payment status
            $sql = "UPDATE payment SET 
                    Payment_Status = 'Cancelled',
                    Payment_DateTime = NOW()
                    WHERE Payment_ID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare payment update statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "i", $row['Payment_ID']);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update payment status: " . mysqli_error($conn));
            }

            // Update order status
            $sql = "UPDATE `order` SET 
                    Order_Status = 'Cancelled',
                    Order_CompletedTime = NOW(),
                    Staff_ID = ?
                    WHERE Order_ID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare order update statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "ii", $staff_id, $order_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update order status: " . mysqli_error($conn));
            }

            // Log the cancellation
            $log_action = "Order cancelled";
            $log_details = sprintf(
                "Order #%d status changed to Cancelled",
                $order_id
            );
            
            $sql = "INSERT INTO logs (Staff_ID, Log_DateTime, Log_Action, Log_Details) 
                    VALUES (?, NOW(), ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare log statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "iss", $staff_id, $log_action, $log_details);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to log cancellation: " . mysqli_error($conn));
            }

            // Commit transaction
            mysqli_commit($conn);

            // Send success response
            echo json_encode([
                'success' => true,
                'message' => 'Order cancelled successfully!'
            ]);

        } catch (Exception $e) {
            mysqli_rollback($conn);
            throw $e;
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log("Error in cancel_order.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
