<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_user_id() {
    return 'user_' . time() . '_' . bin2hex(random_bytes(4));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function send_json_response($success, $message, $data = null) {
    if (ob_get_level()) ob_clean();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function is_user_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function require_user_login() {
    if (!is_user_logged_in()) {
        send_json_response(false, 'Unauthorized. Please login first.');
    }
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        send_json_response(false, 'Unauthorized. Admin access required.');
    }
}
?>
