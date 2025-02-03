<?php
// Error handler function
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    if (error_reporting() & $errno) {
        sendJsonResponse(false, null, "Server error occurred. Please try again.");
    }
    return true; // Don't execute PHP's internal error handler
}

// Exception handler function
function handleException($e) {
    error_log("Uncaught Exception: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse(false, null, 'Server error occurred. Please try again.');
}

// Function to send JSON response
function sendJsonResponse($success, $message = null, $error = null) {
    // Clear any output buffers and previous headers
    while (ob_get_level()) {
        ob_end_clean();
    }
    header_remove();
    
    // Prevent any output before headers
    if (headers_sent($filename, $linenum)) {
        error_log("Headers already sent in $filename on line $linenum");
    }
    
    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Build response
    $response = [
        'success' => $success,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    if ($message !== null) $response['message'] = $message;
    if ($error !== null) $response['error'] = $error;
    
    // Encode with error handling
    $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    if ($json === false) {
        error_log("JSON encode error: " . json_last_error_msg());
        // Fallback response
        $json = json_encode([
            'success' => false,
            'error' => 'Server error: Failed to encode response'
        ]);
    }
    
    // Send response and exit
    echo $json;
    exit();
}

// Start output buffering to catch any unwanted output
ob_start();

// Set error reporting and handlers
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
set_error_handler('handleError');
set_exception_handler('handleException');

// Start session
session_start();

// Set flags for database connection
$isJsonResponse = true;
define('ALLOW_DIRECT_ACCESS', true);

// Include database connection with error handling
try {
    // Include database connection
    require_once 'database_admin.php';
    
    // Verify database connection
    if (!isset($conn)) {
        throw new Exception("Database connection not established");
    }

    // Test database connection
    if (!$conn || !mysqli_ping($conn)) {
        throw new Exception("Database connection lost");
    }

    // Set the connection to use UTF8
    mysqli_set_charset($conn, 'utf8mb4');

    // Debug logging
    error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST data: " . print_r($_POST, true));
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("Headers: " . print_r(getallheaders(), true));
    error_log("Database connection status: " . ($conn ? "Connected" : "Not connected"));

    // Check if user is not logged in
    if(!isset($_SESSION["user_id"])) {
        sendJsonResponse(false, null, 'User not logged in');
    }

    // Validate required fields
    $required_fields = ['order_id', 'discount_type', 'discount_percent', 'discount_amount', 'final_amount'];
    $missing_fields = array_filter($required_fields, function($field) {
        return !isset($_POST[$field]);
    });

    if (!empty($missing_fields)) {
        sendJsonResponse(false, null, 'Missing required fields: ' . implode(', ', $missing_fields));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Validate and sanitize input
            $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
            if ($order_id === false || $order_id <= 0) {
                throw new Exception('Invalid order ID');
            }

            $discount_type = trim($_POST['discount_type'] ?? '');
            $discount_percent = filter_var($_POST['discount_percent'] ?? 0, FILTER_VALIDATE_FLOAT);
            $discount_amount = filter_var($_POST['discount_amount'] ?? 0, FILTER_VALIDATE_FLOAT);
            $final_amount = filter_var($_POST['final_amount'] ?? 0, FILTER_VALIDATE_FLOAT);
            
            if ($final_amount === false || $final_amount < 0) {
                throw new Exception('Invalid final amount');
            }

            $reference_number = trim($_POST['reference_number'] ?? '');
            $cash_paid = filter_var($_POST['cash_paid'] ?? 0, FILTER_VALIDATE_FLOAT);
            $change = filter_var($_POST['change'] ?? 0, FILTER_VALIDATE_FLOAT);

            // Debug logging
            error_log("Received order confirmation data: " . json_encode([
                'order_id' => $order_id,
                'discount_type' => $discount_type,
                'discount_percent' => $discount_percent,
                'discount_amount' => $discount_amount,
                'final_amount' => $final_amount,
                'reference_number' => $reference_number,
                'cash_paid' => $cash_paid,
                'change' => $change
            ]));
            
// Test database connection before starting transaction
if (!mysqli_ping($conn)) {
    error_log("Database connection lost before transaction");
    throw new Exception("Database connection lost. Please try again.");
}

// Set session variables
mysqli_query($conn, "SET SESSION wait_timeout = 300");
mysqli_query($conn, "SET SESSION interactive_timeout = 300");
mysqli_query($conn, "SET SESSION group_concat_max_len = 1000000");

// Start transaction
mysqli_begin_transaction($conn);
error_log("Transaction started successfully");
            
            // Debug database connection
            error_log("Database connection state before query: " . (mysqli_ping($conn) ? "Connected" : "Disconnected"));

            // Get the Payment_ID, method, and date from the order
            $sql = "SELECT o.Payment_ID, o.Payment_Method, o.Order_Status, o.Order_DateTime, o.Order_TicketNumber,
                           p.Payment_Status
                    FROM `order` o 
                    JOIN payment p ON o.Payment_ID = p.Payment_ID
                    WHERE o.Order_ID = ? 
                    AND DATE(o.Order_DateTime) = CURDATE()
                    FOR UPDATE";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $order_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            
            if (!$row) {
                error_log("Order not found - Order ID: " . $order_id);
                throw new Exception("Order not found or not from today");
            }

            // Validate payment status
            if ($row['Payment_Status'] === 'Completed') {
                error_log("Payment already completed - Order ID: " . $order_id);
                throw new Exception("This order has already been processed");
            }

            // Debug logging
            error_log("Processing order: " . json_encode([
                'Order_ID' => $order_id,
                'Ticket_Number' => $row['Order_TicketNumber'],
                'Date' => $row['Order_DateTime'],
                'Status' => $row['Order_Status'],
                'Payment_Method' => $row['Payment_Method']
            ]));

            // Check if order is still in Pending status
            if ($row['Order_Status'] !== 'Pending') {
                throw new Exception("Order cannot be confirmed - current status is " . $row['Order_Status']);
            }
            
            $payment_id = $row['Payment_ID'];
            $payment_method = $row['Payment_Method'];
            
            // Validate payment details
            if ($payment_method === 'GCash') {
                if (empty($reference_number)) {
                    throw new Exception("GCash reference number is required");
                }
                // Reset cash values for GCash payments
                $cash_paid = 0;
                $change = 0;
            } else if ($payment_method === 'Cash') {
                if ($cash_paid === false || $cash_paid <= 0) {
                    throw new Exception("Cash amount is required");
                }
                if ($cash_paid < $final_amount) {
                    throw new Exception("Cash amount must be greater than or equal to total amount");
                }
                // Reset reference number for cash payments
                $reference_number = '';
                // Calculate change if not provided
                if ($change === false || $change === null) {
                    $change = $cash_paid - $final_amount;
                }
            }
            
            // Update order status to Preparing
            $sql = "UPDATE `order` SET Order_Status = 'Preparing' WHERE Order_ID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $order_id);
            mysqli_stmt_execute($stmt);
            
            // Format discount type with percentage if applicable
            $formatted_discount_type = '';
            if (!empty($discount_type)) {
                $formatted_discount_type = $discount_type;
                if ($discount_percent > 0) {
                    $formatted_discount_type .= " ({$discount_percent}%)";
                }
            }

            // Update payment with all details
            $sql = "UPDATE payment SET 
                    Payment_Status = 'Completed',
                    Payment_DateTime = NOW(),
                    Payment_DiscountType = ?,
                    Payment_DiscountAmount = ?,
                    Payment_TotalAmount = ?,
                    Payment_ReferenceNumber = ?,
                    Payment_CashPaid = ?,
                    Payment_Change = ?
                    WHERE Payment_ID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            mysqli_stmt_bind_param($stmt, "sddsddj", 
                $formatted_discount_type,
                $discount_amount,
                $final_amount,
                $reference_number,
                $cash_paid,
                $change,
                $payment_id
            );
            mysqli_stmt_execute($stmt);

            // Get order items
            $sql = "SELECT oi.MenuItem_ID, oi.OrderItem_CupSize, oi.OrderItem_Quantity 
                    FROM orderitem oi 
                    WHERE oi.Order_ID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $order_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to fetch order items: " . mysqli_error($conn));
            }
            
            $result = mysqli_stmt_get_result($stmt);
            while ($item = mysqli_fetch_assoc($result)) {
                // Update size stock and sold count
                $sql = "UPDATE menuitem_sizes 
                        SET MenuItemSize_Stock = MenuItemSize_Stock - ?,
                            MenuItemSize_Sold = MenuItemSize_Sold + ?
                        WHERE MenuItem_ID = ? AND MenuItemSize_SizeName = ?";
                $updateStmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($updateStmt, "iiis", 
                    $item['OrderItem_Quantity'],
                    $item['OrderItem_Quantity'],
                    $item['MenuItem_ID'],
                    $item['OrderItem_CupSize']
                );
                
                if (!mysqli_stmt_execute($updateStmt)) {
                    throw new Exception("Failed to update size stock and sold count: " . mysqli_error($conn));
                }

                // Update total stocks and sold count in menuitem table
                $sql = "UPDATE menuitem m 
                        SET MenuItem_TotalStocks = (
                            SELECT SUM(MenuItemSize_Stock) 
                            FROM menuitem_sizes 
                            WHERE MenuItem_ID = m.MenuItem_ID
                        ),
                        MenuItem_TotalSold = (
                            SELECT SUM(MenuItemSize_Sold) 
                            FROM menuitem_sizes 
                            WHERE MenuItem_ID = m.MenuItem_ID
                        )
                        WHERE MenuItem_ID = ?";
                $updateTotalStmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($updateTotalStmt, "i", $item['MenuItem_ID']);
                
                if (!mysqli_stmt_execute($updateTotalStmt)) {
                    throw new Exception("Failed to update total stock and sold count: " . mysqli_error($conn));
                }
            }
            
            // Commit transaction
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                throw new Exception("Failed to commit transaction: " . mysqli_error($conn));
            }

            error_log("Transaction committed successfully - Order ID: " . $order_id);
            sendJsonResponse(true, 'Order confirmed and now preparing!');
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            error_log("Error in confirm_order.php: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            sendJsonResponse(false, null, $e->getMessage());
        }
    } else {
        sendJsonResponse(false, null, 'Invalid request method');
    }
} catch (Exception $e) {
    error_log("Error in confirm_order.php: " . $e->getMessage());
    sendJsonResponse(false, null, $e->getMessage());
}
?>
