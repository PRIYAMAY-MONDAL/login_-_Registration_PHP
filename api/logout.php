<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/functions.php';

header('Content-Type: application/json');

session_unset();
session_destroy();

send_json_response(true, 'Logged out successfully');
?>
