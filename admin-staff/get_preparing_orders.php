<?php
require_once 'database_admin.php';

// Query to get orders that are "Preparing" with "Completed" payment status
$sql = "SELECT o.*, 
              GROUP_CONCAT(CONCAT(oi.OrderItem_Quantity, ' x ', m.MenuItem_Name, ' ', oi.OrderItem_CupSize) SEPARATOR '<br>') as items,
              p.Payment_DateTime,
              p.Payment_TotalAmount,
              p.Payment_Method
       FROM `order` o
       JOIN orderitem oi ON o.Order_ID = oi.Order_ID
       JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
       JOIN payment p ON o.Payment_ID = p.Payment_ID
       WHERE o.Order_Status = 'Preparing' 
       AND p.Payment_Status = 'Completed'
       GROUP BY o.Order_ID
       ORDER BY p.Payment_DateTime ASC";

$result = mysqli_query($conn, $sql);
$orders = array();

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $orders[] = array(
            'order_id' => $row['Order_ID'],
            'ticket_number' => $row['Order_TicketNumber'],
            'eating_option' => $row['Order_EatingOption'],
            'items' => $row['items'],
            'payment_method' => $row['Payment_Method'],
            'payment_datetime' => date('M d, Y h:i A', strtotime($row['Payment_DateTime'])),
            'total_amount' => number_format($row['Payment_TotalAmount'], 2)
        );
    }
    echo json_encode(['success' => true, 'orders' => $orders]);
} else {
    echo json_encode(['success' => true, 'orders' => []]);
}

mysqli_close($conn);
?>
