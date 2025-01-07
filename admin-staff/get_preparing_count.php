<?php
require_once 'database_admin.php';

header('Content-Type: application/json');

$sql = "SELECT COUNT(*) as count FROM `order` WHERE Order_Status = 'Preparing'";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'count' => $row['count']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => mysqli_error($conn)
    ]);
}
?>
