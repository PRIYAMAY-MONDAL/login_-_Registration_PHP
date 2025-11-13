<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

$action = $_POST['action'] ?? '';

if ($action === 'verify_email') {
    $email = sanitize_input($_POST['email'] ?? '');
    
    if (empty($email) || !validate_email($email)) {
        send_json_response(false, 'Valid email is required');
    }
    
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        send_json_response(false, 'Email not found. Please register first.');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    send_json_response(true, 'Email verified', ['user_id' => $user['id']]);
    
} else if ($action === 'reset_password') {
    $email = sanitize_input($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (empty($email) || empty($current_password) || empty($new_password)) {
        send_json_response(false, 'All fields are required');
    }
    
    if (strlen($new_password) < PASSWORD_MIN_LENGTH) {
        send_json_response(false, 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
    }
    
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        send_json_response(false, 'User not found');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!verify_password($current_password, $user['password'])) {
        send_json_response(false, 'Current password is incorrect');
    }
    
    $hashed_password = hash_password($new_password);
    
    $stmt = $conn->prepare("UPDATE users SET password = ?, password_updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user['id']);
    
    if ($stmt->execute()) {
        $stmt->close();
        send_json_response(true, 'Password reset successful!');
    } else {
        $stmt->close();
        send_json_response(false, 'Failed to reset password');
    }
}

send_json_response(false, 'Invalid action');

$conn->close();
?>
