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
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');

if (empty($user_id) || empty($name) || empty($email)) {
    send_json_response(false, 'User ID, name, and email are required');
}

if (!validate_email($email)) {
    send_json_response(false, 'Invalid email format');
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    send_json_response(false, 'Email already in use by another user');
}
$stmt->close();

$stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
$stmt->bind_param("sssi", $name, $email, $phone, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    send_json_response(true, 'User updated successfully');
} else {
    $stmt->close();
    send_json_response(false, 'Failed to update user');
}

$conn->close();
?>
