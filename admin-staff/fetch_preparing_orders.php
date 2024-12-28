<?php
require_once 'database_admin.php';

header('Content-Type: application/json');

try {
    // Query to get orders that are "Preparing" with "Completed" payment status
    $sql = "SELECT 
                o.Order_ID,
                o.Order_TicketNumber,
                o.Order_Status,
                o.Order_DateTime,
                o.Order_EatingOption,
                o.Order_TotalAmount,
                GROUP_CONCAT(
                    CONCAT(
                        oi.OrderItem_Quantity, 
                        ' x ', 
                        m.MenuItem_Name, 
                        ' (', 
                        oi.OrderItem_CupSize,
                        ')'
                    ) 
                    SEPARATOR '<br>'
                ) as items,
                p.Payment_Method,
                p.Payment_DateTime
            FROM `order` o
            JOIN orderitem oi ON o.Order_ID = oi.Order_ID
            JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
            JOIN payment p ON o.Payment_ID = p.Payment_ID
            WHERE o.Order_Status = 'Preparing' 
            AND p.Payment_Status = 'Completed'
            GROUP BY o.Order_ID
            ORDER BY o.Order_DateTime ASC";
            
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $orders = array();
        while($row = mysqli_fetch_assoc($result)) {
            $orders[] = array(
                'order_id' => $row['Order_ID'],
                'ticket_number' => $row['Order_TicketNumber'],
                'status' => $row['Order_Status'],
                'datetime' => date('M d, Y h:i A', strtotime($row['Order_DateTime'])),
                'eating_option' => $row['Order_EatingOption'],
                'items' => $row['items'],
                'payment_method' => $row['Payment_Method'],
                'payment_datetime' => date('M d, Y h:i A', strtotime($row['Payment_DateTime'])),
                'total_amount' => number_format($row['Order_TotalAmount'], 2)
            );
        }
        
        echo json_encode([
            'success' => true,
            'orders' => $orders,
            'message' => count($orders) > 0 ? null : 'No orders in preparation at the moment.'
        ]);
    } else {
        throw new Exception(mysqli_error($conn));
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
