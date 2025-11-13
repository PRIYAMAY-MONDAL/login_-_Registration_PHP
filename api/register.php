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

$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($name)) $errors[] = 'Name is required';
if (empty($email)) $errors[] = 'Email is required';
else if (!validate_email($email)) $errors[] = 'Invalid email format';
if (empty($password)) $errors[] = 'Password is required';
else if (strlen($password) < PASSWORD_MIN_LENGTH) $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';

if (!empty($errors)) {
    send_json_response(false, implode(', ', $errors));
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    send_json_response(false, 'Email already registered');
}
$stmt->close();

$user_id = generate_user_id();
$hashed_password = hash_password($password);

$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, user_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $user_id);

if ($stmt->execute()) {
    $inserted_id = $stmt->insert_id;
    $stmt->close();
    
    $_SESSION['user_id'] = $inserted_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = 'user';
    
    send_json_response(true, 'Registration successful!', [
        'id' => $inserted_id,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'user_id' => $user_id
    ]);
} else {
    $stmt->close();
    send_json_response(false, 'Registration failed');
}

$conn->close();
?>
