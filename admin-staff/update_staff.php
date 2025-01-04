<?php
include 'database_admin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false);
    
    try {
        $staffId = $_POST['staffId'];
        $firstName = $_POST['firstName'];
        $middleName = $_POST['middleName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $contactNumber = $_POST['contactNumber'];
        $address = $_POST['address'];
        $birthDate = $_POST['birthDate'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        // Check if email exists for other staff members
        $check_sql = "SELECT * FROM staff WHERE Staff_Email = ? AND Staff_ID != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $staffId);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = "Email already exists";
            echo json_encode($response);
            exit;
        }

        // Update staff
        $sql = "UPDATE staff SET 
                Staff_FirstName = ?, 
                Staff_MiddleName = ?, 
                Staff_LastName = ?, 
                Staff_Email = ?, 
                Staff_ContactNumber = ?, 
                Staff_Address = ?, 
                Staff_BirthDate = ?, 
                Staff_Role = ?, 
                Staff_Status = ? 
                WHERE Staff_ID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", 
            $firstName, 
            $middleName, 
            $lastName, 
            $email, 
            $contactNumber, 
            $address, 
            $birthDate, 
            $role, 
            $status, 
            $staffId
        );
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Staff updated successfully";
        } else {
            $response['message'] = "Error updating staff";
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
    
    echo json_encode($response);
}

$conn->close();
?>
