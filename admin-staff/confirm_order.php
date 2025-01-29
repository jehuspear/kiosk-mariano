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
