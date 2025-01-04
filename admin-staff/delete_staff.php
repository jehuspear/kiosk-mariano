<?php
include 'database_admin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $response = array('success' => false);
    
    try {
        $staffId = $_POST['id'];
        
        // Check if staff exists
        $check_sql = "SELECT * FROM staff WHERE Staff_ID = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $staffId);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows === 0) {
            $response['message'] = "Staff not found";
            echo json_encode($response);
            exit;
        }

        // Check if staff has associated orders
        $order_check_sql = "SELECT * FROM `order` WHERE Staff_ID = ?";
        $order_check_stmt = $conn->prepare($order_check_sql);
        $order_check_stmt->bind_param("i", $staffId);
        $order_check_stmt->execute();
        $order_result = $order_check_stmt->get_result();

        if ($order_result->num_rows > 0) {
            // If staff has orders, update status to Inactive instead of deleting
            $update_sql = "UPDATE staff SET Staff_Status = 'Inactive' WHERE Staff_ID = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $staffId);
            
            if ($update_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Staff has associated orders. Status changed to Inactive instead of deletion.";
            } else {
                $response['message'] = "Error updating staff status";
            }
        } else {
            // If no orders, proceed with deletion
            $delete_sql = "DELETE FROM staff WHERE Staff_ID = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $staffId);
            
            if ($delete_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Staff deleted successfully";
            } else {
                $response['message'] = "Error deleting staff";
            }
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
    
    echo json_encode($response);
}

$conn->close();
?>
