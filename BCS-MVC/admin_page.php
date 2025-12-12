<?php

require_once "Views/template/header.phtml";
// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// load required classes
require_once('Models/UsersDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'My Posts';

// Access Control: Must be logged in AND must be an admin
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$adminId = $_SESSION['user_id'];

$UsersDataSet = new UsersDataSet();

// Fetch students and employers
$view->placement = $UsersDataSet->fetchAllUsers();

if (isset($_POST['delete_user'])) {
    $user = $_POST['delete_user'];
    $deleting = $UsersDataSet->delete_user($user);

    header('Location: admin_page.php');
    exit;
}


require_once('Views/admin_page.phtml');
