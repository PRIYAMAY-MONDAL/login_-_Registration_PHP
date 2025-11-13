<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT id, name, email, phone, user_id, registered_at, last_login FROM users WHERE is_active = 1 ORDER BY registered_at DESC");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
$conn->close();

send_json_response(true, 'Users retrieved successfully', ['users' => $users]);
?>
