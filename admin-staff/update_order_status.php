<?php
// This file is deprecated. Order status updates are now handled in confirm_order.php
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'error' => 'This endpoint is deprecated. Please use confirm_order.php instead.'
]);
exit();
?>
