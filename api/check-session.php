<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (empty($_SESSION) || !isset($_SESSION['user_role'])) {
    send_json_response(false, 'No active session', ['role' => null]);
}

if ($_SESSION['user_role'] === 'admin') {
    if (!isset($_SESSION['admin_id'])) {
        send_json_response(false, 'Invalid admin session', ['role' => null]);
    }
    
    send_json_response(true, 'Admin session active', [
        'role' => 'admin',
        'id' => $_SESSION['admin_id'],
        'name' => $_SESSION['admin_name'] ?? 'Administrator',
        'email' => $_SESSION['admin_email'] ?? ''
    ]);
}

if ($_SESSION['user_role'] === 'user') {
    if (!isset($_SESSION['user_id'])) {
        send_json_response(false, 'Invalid user session', ['role' => null]);
    }
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, name, email, phone, user_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        
        send_json_response(true, 'User session active', [
            'role' => 'user',
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'user_id' => $user['user_id']
        ]);
    }
    $stmt->close();
}

send_json_response(false, 'Invalid session state', ['role' => null]);

$conn->close();
?>
