<?php
session_start();
require_once('Models/UsersDataSet.php');

$usersDataSet = new UsersDataSet();

$view = new stdClass();
$view->error = "";
$view->success = "";
$view->token = $_GET['token'] ?? '';

// Hash the token from URL to compare with database
$hashedToken = hash('sha256', $view->token);
$user = $usersDataSet->getUserByToken($hashedToken);

if (!$user) {
    $view->error = "Invalid or expired reset link.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validate password
    if (empty($password)) {
        $view->error = "Password cannot be empty.";
    } elseif (strlen($password) < 8) {
        $view->error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm) {
        $view->error = "Passwords do not match.";
    } else {
        // Update password and clear reset token
        if ($usersDataSet->updatePassword($user->getUserID(), $password)) {
            // Redirect to login with success message
            header("Location: login.php?resetSuccess=1");
            exit;
        } else {
            $view->error = "Failed to update password. Please try again.";
        }
    }
}

require('Views/reset.phtml');
?>