<?php
include 'database_admin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false);
    
    try {
        // Get form data
        $firstName = $_POST['firstName'];
        $middleName = $_POST['middleName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $contactNumber = $_POST['contactNumber'];
        $address = $_POST['address'];
        $birthDate = $_POST['birthDate'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        // Check if email or username already exists
        $check_sql = "SELECT * FROM staff WHERE Staff_Email = ? OR Staff_Username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $email, $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = "Email or username already exists";
            echo json_encode($response);
            exit;
        }

        // Insert new staff
        $sql = "INSERT INTO staff (Staff_FirstName, Staff_MiddleName, Staff_LastName, Staff_Email, Staff_Username, Staff_Password, Staff_ContactNumber, Staff_Address, Staff_BirthDate, Staff_Role, Staff_Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", 
            $firstName, 
            $middleName, 
            $lastName, 
            $email, 
            $username, 
            $password, 
            $contactNumber, 
            $address, 
            $birthDate, 
            $role, 
            $status
        );
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Staff added successfully";
        } else {
            $response['message'] = "Error adding staff";
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
    
    echo json_encode($response);
}

$conn->close();
?>
