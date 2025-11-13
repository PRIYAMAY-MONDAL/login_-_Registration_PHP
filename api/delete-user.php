<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

$user_id = intval($_POST['user_id'] ?? 0);

if (empty($user_id)) {
    send_json_response(false, 'User ID is required');
}

$stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $stmt->close();
    send_json_response(true, 'User deleted successfully');
} else {
    $stmt->close();
    send_json_response(false, 'Failed to delete user');
}

$conn->close();
?>
