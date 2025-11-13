<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/functions.php';

require_user_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

$user_id = $_SESSION['user_id'];
$name = sanitize_input($_POST['name'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');

if (empty($name)) {
    send_json_response(false, 'Name is required');
}

$stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $phone, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['user_name'] = $name;
    
    send_json_response(true, 'Profile updated successfully!', [
        'name' => $name,
        'phone' => $phone
    ]);
} else {
    $stmt->close();
    send_json_response(false, 'Failed to update profile');
}

$conn->close();
?>
