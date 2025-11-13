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

if (!validate_email($email)) {
    send_json_response(false, 'Invalid email format');
}

$stmt = $conn->prepare("SELECT id, name, email, phone, password, user_id FROM users WHERE email = ? AND is_active = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    send_json_response(false, 'Invalid credentials');
}

$user = $result->fetch_assoc();
$stmt->close();

if (!verify_password($password, $user['password'])) {
    send_json_response(false, 'Invalid credentials');
}

$stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$stmt->close();

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = 'user';
$_SESSION['login_time'] = time();

send_json_response(true, 'Login successful!', [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'],
    'user_id' => $user['user_id']
]);

$conn->close();
?>
