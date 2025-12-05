<?php
// backend/logout.php
declare(strict_types=1);

require __DIR__ . '/config.php';

// Clear session array
$_SESSION = [];

// Remove session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy session
session_destroy();

// Redirect to login with a small flag (you can use it later for a "Logged out" message)
header('Location: ../public/login.html?logged_out=1');
exit;
