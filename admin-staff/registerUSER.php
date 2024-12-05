<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = mysqli_connect('localhost', 'root', '', 'kiosk_ordering_system_db');
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
} else {
    echo "Database connected successfully.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize inputs
    $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    // Validate inputs
    if ($password_1 !== $password_2) {
        echo "Passwords do not match!";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password_1, PASSWORD_BCRYPT);

    // Start transaction
    mysqli_begin_transaction($db);

    try {
        // Insert into staff table
        $insert_staff_query = "INSERT INTO staff (Firstname, Lastname, Username, Password) 
                               VALUES ('$firstname', '$lastname', '$username', '$hashed_password')";
        if (!mysqli_query($db, $insert_staff_query)) {
            throw new Exception("Error inserting staff data: " . mysqli_error($db));
        }

        // Get the Staff_ID of the newly inserted record
        $staff_id = mysqli_insert_id($db);

        // Insert log into logs table
        $log_action = 'Registration';
        $log_details = 'New staff registered: ' . $firstname . ' ' . $lastname . ' (Username: ' . $username . ')';
        $insert_log_query = "INSERT INTO logs (Staff_ID, Log_Action, Log_Details) 
                             VALUES ('$staff_id', '$log_action', '$log_details')";
        if (!mysqli_query($db, $insert_log_query)) {
            throw new Exception("Error inserting log data: " . mysqli_error($db));
        }

        // Commit transaction
        mysqli_commit($db);

        echo "Registration successful!";
        header('Location: login.php');
        exit;

    } catch (Exception $e) {
        // Rollback transaction in case of error
        mysqli_rollback($db);
        echo $e->getMessage();
    }
}
?>