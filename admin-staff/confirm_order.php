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
            $sql = "SELECT o.Order_Status, o.Payment_ID, o.Payment_Method, p.Payment_Status 
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

            // Get payment data with proper validation
            $payment_id = $row['Payment_ID'];
            $payment_method = $row['Payment_Method'];

            // Validate discount data
            $discount_type = trim($_POST['discount_type'] ?? '');
            $discount_percent = filter_var($_POST['discount_percent'] ?? 0, FILTER_VALIDATE_FLOAT);
            $discount_amount = filter_var($_POST['discount_amount'] ?? 0, FILTER_VALIDATE_FLOAT);
            $final_amount = filter_var($_POST['final_amount'] ?? 0, FILTER_VALIDATE_FLOAT);

            if ($final_amount <= 0) {
                throw new Exception("Invalid final amount");
            }

            // Validate payment method specific data
            $reference_number = '';
            $cash_paid = 0.00;
            $change = 0.00;

            if ($payment_method === 'GCash') {
                $reference_number = trim($_POST['reference_number'] ?? '');
                if (strlen($reference_number) !== 6 || !ctype_digit($reference_number)) {
                    throw new Exception("Invalid GCash reference number");
                }
            } else if ($payment_method === 'Cash') {
                $cash_paid = filter_var($_POST['cash_paid'] ?? 0, FILTER_VALIDATE_FLOAT);
                if ($cash_paid < $final_amount) {
                    throw new Exception("Cash paid must be greater than or equal to final amount");
                }
                $change = $cash_paid - $final_amount;
            } else {
                throw new Exception("Invalid payment method");
            }

            // Format discount type if percentage is provided
            if (!empty($discount_type) && $discount_percent > 0) {
                $discount_type .= " ({$discount_percent}%)";
            }

            // Set timezone to Philippines
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = date('Y-m-d H:i:s');

            // Update payment details
            $sql = "UPDATE payment SET 
                    Payment_Status = 'Completed',
                    Payment_DateTime = ?,
                    Payment_DiscountType = ?,
                    Payment_DiscountAmount = ?,
                    Payment_TotalAmount = ?,
                    Payment_ReferenceNumber = ?,
                    Payment_CashPaid = ?,
                    Payment_Change = ?
                    WHERE Payment_ID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare update statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "ssddsddi", 
                $currentDateTime,
                $discount_type,
                $discount_amount,
                $final_amount,
                $reference_number,
                $cash_paid,
                $change,
                $payment_id
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update payment: " . mysqli_error($conn));
            }
            //The Staff who Handles that Order
            $staff_id = $_SESSION['user_id'];
            // Get order items with their size IDs to update stock and sold counts
            $sql = "SELECT oi.MenuItem_ID, ms.MenuItemSize_ID, oi.OrderItem_Quantity 
                   FROM orderitem oi 
                   JOIN menuitem_sizes ms ON ms.MenuItem_ID = oi.MenuItem_ID 
                   AND ms.MenuItemSize_SizeName = oi.OrderItem_CupSize
                   WHERE oi.Order_ID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare order items statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "i", $order_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to get order items: " . mysqli_error($conn));
            }

            $result = mysqli_stmt_get_result($stmt);
            while ($item = mysqli_fetch_assoc($result)) {
                // Update menuitem_sizes table
                $updateSizeSql = "UPDATE menuitem_sizes 
                                SET MenuItemSize_Stock = MenuItemSize_Stock - ?,
                                    MenuItemSize_Sold = MenuItemSize_Sold + ?
                                WHERE MenuItemSize_ID = ?";
                
                $updateSizeStmt = mysqli_prepare($conn, $updateSizeSql);
                if (!$updateSizeStmt) {
                    throw new Exception("Failed to prepare size update statement: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($updateSizeStmt, "iii", 
                    $item['OrderItem_Quantity'],
                    $item['OrderItem_Quantity'],
                    $item['MenuItemSize_ID']
                );
                
                if (!mysqli_stmt_execute($updateSizeStmt)) {
                    throw new Exception("Failed to update size stock and sold count: " . mysqli_error($conn));
                }

                // Update total stocks and total sold in menuitem table
                $updateItemSql = "UPDATE menuitem m 
                                SET m.MenuItem_TotalStocks = (
                                    SELECT COALESCE(SUM(ms.MenuItemSize_Stock), 0)
                                    FROM menuitem_sizes ms 
                                    WHERE ms.MenuItem_ID = m.MenuItem_ID
                                ),
                                m.MenuItem_TotalSold = (
                                    SELECT COALESCE(SUM(ms.MenuItemSize_Sold), 0)
                                    FROM menuitem_sizes ms 
                                    WHERE ms.MenuItem_ID = m.MenuItem_ID
                                )
                                WHERE m.MenuItem_ID = ?";
                
                $updateItemStmt = mysqli_prepare($conn, $updateItemSql);
                if (!$updateItemStmt) {
                    throw new Exception("Failed to prepare item update statement: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($updateItemStmt, "i", $item['MenuItem_ID']);
                
                if (!mysqli_stmt_execute($updateItemStmt)) {
                    throw new Exception("Failed to update item total stocks: " . mysqli_error($conn));
                }
            }

            // Update order status
            $sql = "UPDATE `order` SET 
                    Order_Status = 'Preparing',
                    Order_CompletedTime = ?,
                    Staff_ID = $staff_id
                    WHERE Order_ID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare order update statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "si", $currentDateTime, $order_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update order status: " . mysqli_error($conn));
            }

            // Log the payment
            
            $log_action = "Payment processed";
            $log_details = sprintf(
                "Order #%d payment processed. Method: %s, Amount: ₱%.2f, Discount: %s",
                $order_id,
                $payment_method,
                $final_amount,
                $discount_type ? $discount_type : 'None'
            );
            
            $sql = "INSERT INTO logs (Staff_ID, Log_DateTime, Log_Action, Log_Details) 
                    VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare log statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "isss", $staff_id, $currentDateTime, $log_action, $log_details);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to log payment: " . mysqli_error($conn));
            }

            // Commit transaction
            mysqli_commit($conn);

            // Send success response
            echo json_encode([
                'success' => true,
                'message' => 'Order confirmed and now preparing!'
            ]);

        } catch (Exception $e) {
            mysqli_rollback($conn);
            throw $e;
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log("Error in confirm_order.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
