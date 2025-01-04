<?php
include 'database_admin.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    // Get specific staff member
    $id = $_GET['id'];
    $sql = "SELECT * FROM staff WHERE Staff_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
} else {
    // Get all staff members
    $sql = "SELECT * FROM staff ORDER BY Staff_LastName";
    $result = $conn->query($sql);
    $staff = array();
    
    while ($row = $result->fetch_assoc()) {
        $staff[] = $row;
    }
    
    echo json_encode($staff);
}

$conn->close();
?>
