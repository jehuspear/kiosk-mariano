<?php
// Prevent direct access
if (!defined('ALLOW_DIRECT_ACCESS') && basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    exit('Direct access not permitted');
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("Starting database connection - " . date('Y-m-d H:i:s'));

// Database configuration
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "kiosk_ordering_system_db";

// Debug logging function
function logDebug($message, $data = null) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $logMessage .= " - Data: " . print_r($data, true);
    }
    error_log($logMessage);
}

try {
    logDebug("Attempting database connection");
    logDebug("Connection details", [
        'host' => $hostName,
        'user' => $dbUser,
        'database' => $dbName
    ]);

    // Create connection with error suppression
    $conn = @mysqli_connect($hostName, $dbUser, $dbPassword);
    if (!$conn) {
        $error = mysqli_connect_error();
        logDebug("Basic connection failed", ['error' => $error]);
        throw new Exception("Database connection failed: " . $error);
    }
    logDebug("Basic connection successful");

    // Set connection timeout
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

    // Select database
    if (!mysqli_select_db($conn, $dbName)) {
        throw new Exception("Database selection failed: " . mysqli_error($conn));
    }
    logDebug("Database selection successful");

    // Set charset
    if (!mysqli_set_charset($conn, "utf8mb4")) {
        throw new Exception("Charset setting failed: " . mysqli_error($conn));
    }
    logDebug("Charset setting successful");

    // Test query
    $testResult = mysqli_query($conn, "SELECT 1");
    if (!$testResult) {
        throw new Exception("Test query failed: " . mysqli_error($conn));
    }
    logDebug("Test query successful");

    // Set essential session variables with error handling
    $queries = [
        ["SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'", "SQL mode"],
        ["SET SESSION time_zone='+08:00'", "Time zone"], // Set to Philippines timezone
        ["SET NAMES utf8mb4", "Character set"]
    ];

    foreach ($queries as [$query, $description]) {
        if (!mysqli_query($conn, $query)) {
            $error = mysqli_error($conn);
            logDebug("Failed to set $description", ['error' => $error]);
            // Log error but don't throw exception for non-critical settings
            error_log("Warning: Failed to set $description: " . $error);
        }
    }
    logDebug("Session variables set successfully");

    // Set basic connection options
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 20);
    
    // Keep autocommit on by default
    mysqli_autocommit($conn, true);

    // Enable error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    logDebug("Error reporting enabled");

    // Final connection test
    if (!mysqli_ping($conn)) {
        throw new Exception("Final connection test failed");
    }
    logDebug("Final connection test successful");

} catch (Exception $e) {
    logDebug("Database connection error", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Clean up connection if it exists
    if (isset($conn) && $conn) {
        mysqli_close($conn);
        logDebug("Existing connection closed");
    }
    
    // Let the calling script handle the error response
    throw new Exception("Database connection failed: " . $e->getMessage());
}

// Log successful connection
logDebug("Database connection fully established and configured");
?>
