<?php
// ============================================
// File: logout.php - Destroy session
// ============================================
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect with message
header("Location: login.php?msg=loggedout");
exit();
?>