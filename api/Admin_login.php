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

$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    send_json_response(false, 'Email and password are required');
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM admins WHERE email = ? AND is_active = 1");

if (!$stmt) {
    send_json_response(false, 'Database error');
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    send_json_response(false, 'Invalid admin credentials');
}

$admin = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $admin['password'])) {
    send_json_response(false, 'Invalid admin credentials');
}

$stmt = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
$stmt->bind_param("i", $admin['id']);
$stmt->execute();
$stmt->close();

$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_name'] = $admin['name'];
$_SESSION['user_role'] = 'admin';
$_SESSION['login_time'] = time();

send_json_response(true, 'Admin login successful!', [
    'id' => $admin['id'],
    'name' => $admin['name'],
    'email' => $admin['email'],
    'role' => $admin['role']
]);

$conn->close();
?>
