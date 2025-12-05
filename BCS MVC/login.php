<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('Models/UsersDataSet.php');

$view = new stdClass();
$view->pageTitle = 'User Login';
$view->loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $usersDataSet = new UsersDataSet();
    $user = $usersDataSet->findUserAndVerifyPassword($email, $password);
    if ($user !== false) {

        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->getUserID();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['name'] = $user->getName();
        $_SESSION['phone_number'] = $user->getPhoneNumber();
        $_SESSION['postal_address'] = $user->getPostalAddress();
        $_SESSION['cv_file_path'] = $user->getCvFilePath();

    } else {
        $view->loginError = 'Invalid email or password.';
    }
}

// Load the View
require_once('Views/login.phtml');