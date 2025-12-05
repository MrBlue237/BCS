<?php
session_start();

if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {

    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    header('Location: index.php');
    exit;
}

header('Location: login.php');
exit;