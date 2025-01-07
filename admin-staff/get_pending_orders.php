<?php
require_once 'database_admin.php';

header('Content-Type: application/json');

$sql = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.OrderItem_Quantity, ' x ', m.MenuItem_Name, ' ', oi.OrderItem_CupSize) SEPARATOR '<br>') as items
        FROM `order` o
        JOIN orderitem oi ON o.Order_ID = oi.Order_ID
        JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
        WHERE o.Order_Status = 'Pending'
        GROUP BY o.Order_ID
        ORDER BY o.Order_DateTime DESC";

$result = mysqli_query($conn, $sql);
$orders = [];

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $orders[] = [
            'html' => generateOrderHtml($row),
            'id' => $row['Order_ID']
        ];
    }
}

function generateOrderHtml($row) {
    return '
    <div class="order" data-order-id="' . htmlspecialchars($row['Order_ID']) . '">
        <div class="order-item">' . htmlspecialchars($row['Order_TicketNumber']) . '</div>
        <div class="order-item">' . htmlspecialchars($row['Order_EatingOption']) . '</div>
        <div class="order-item">' . htmlspecialchars($row['Order_ID']) . '</div>
        <div class="order-item">' . $row['items'] . '</div>
        <div class="order-item">' . htmlspecialchars($row['Payment_Method']) . '</div>
        <div class="order-item">' . date('m/d/y', strtotime($row['Order_DateTime'])) . '</div>
        <div class="order-buttons">
            <button class="button add-button" style="margin-left:40px">Add</button>
        </div>
        <div class="order-item">₱' . number_format($row['Order_TotalAmount'], 2) . '</div>
        <div class="order-buttons">
            <button class="button done-button" style="margin-left:40px" data-order-id="' . $row['Order_ID'] . '">Confirm</button>
            <button class="button-cancel" style="margin-left:40px" data-order-id="' . $row['Order_ID'] . '">Cancel</button>
        </div>
    </div>';
}

echo json_encode([
    'success' => true,
    'orders' => $orders
]);
?>
