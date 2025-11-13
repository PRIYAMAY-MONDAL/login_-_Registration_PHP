<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'user_admin_system');

// Application configuration
define('APP_NAME', 'User Admin System');
define('BASE_URL', 'http://localhost/USERLOGIN/');

// Security settings
define('SESSION_LIFETIME', 86400);
define('PASSWORD_MIN_LENGTH', 6);

// Disable error display for production
ini_set('display_errors', 0);
error_reporting(0);

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Database connection failed']));
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

date_default_timezone_set('Asia/Kolkata');
?>
