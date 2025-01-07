<?php
require_once 'database_customer.php';

// Update orders that have been in ReadyToClaim status for more than 10 minutes
$sql = "UPDATE `order` 
        SET Order_Status = 'Completed' 
        WHERE Order_Status = 'ReadyToClaim' 
        AND Order_CompletedTime IS NOT NULL 
        AND TIMESTAMPDIFF(MINUTE, Order_CompletedTime, NOW()) > 10";

$result = $conn->query($sql);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'updated' => $conn->affected_rows
]);

$conn->close();
?>
