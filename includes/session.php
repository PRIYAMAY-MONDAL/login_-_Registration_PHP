<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 86400);
    ini_set('session.gc_maxlifetime', 86400);
    session_start();
}

if (isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
} else {
    $_SESSION['last_activity'] = time();
}
?>
