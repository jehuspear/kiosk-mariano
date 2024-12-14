<?php
session_start();

// Database connection setup
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'kiosk_ordering_system_db';

// Connect to the database
$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize error variable
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Query to fetch user record
    $query = "SELECT * FROM staff WHERE Username = '$username'";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['Password'])) {
            // Store user information in session
            $_SESSION['Staff_ID'] = $user['Staff_ID'];
            $_SESSION['Firstname'] = $user['Firstname'];
            $_SESSION['Lastname'] = $user['Lastname'];
            $_SESSION['Username'] = $user['Username'];

            // Log the login action
            $staff_id = $user['Staff_ID'];
            $log_action = 'Login';
            $log_details = 'User logged in: ' . $user['Firstname'] . ' ' . $user['Lastname'];
            $log_query = "INSERT INTO logs (Staff_ID, Log_Action, Log_Details) 
                          VALUES ('$staff_id', '$log_action', '$log_details')";
            mysqli_query($db, $log_query);

            // Redirect to menuscreen.php
            header('Location: menuscreen.php');
            exit;
        } else {
            // Invalid password
            $error = "Invalid username or password.";
        }
    } else {
        // No user found
        $error = "Invalid username or password.";
    }

    // If there's an error, redirect to login.php with error message
    if ($error) {
        header("Location: login.php?error=" . urlencode($error));
        exit;
    }
}
?>
