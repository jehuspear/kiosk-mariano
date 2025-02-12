<?php
    // Database configuration from environment variables
    $hostName = getenv('DB_HOST') ?: 'localhost';
    $dbUser = getenv('DB_USER') ?: 'root';
    $dbPassword = getenv('DB_PASSWORD') ?: '';
    $dbName = getenv('DB_NAME') ?: 'kiosk_ordering_system_db';

    // Create connection with error handling
    $conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
    if (!$conn) {
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Database connection failed. Please try again later.");
    }

    // Set charset
    if (!mysqli_set_charset($conn, "utf8mb4")) {
        error_log("Failed to set charset: " . mysqli_error($conn));
        die("Configuration error. Please try again later.");
    }

    // Set timezone
    if (!mysqli_query($conn, "SET time_zone='+08:00'")) {
        error_log("Failed to set timezone: " . mysqli_error($conn));
        die("Configuration error. Please try again later.");
    }
?>
