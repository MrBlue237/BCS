<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Models
require_once('Models/StudentDataSet.php');
require_once('Models/UsersDataSet.php');

// 1. Access Control: Must be logged in AND must be a student
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] ?? '') !== 'employer') {
    header('Location: login.php'); // Redirect non-employers and not-logged-in users
    exit;
}

$emp_id = $_SESSION['user_id'];
$DataSet = new StudentDataSet();
$view = new stdClass();
$view->pageTitle = 'Employer Profile';
$view->updateSuccess = false;
$view->updateError = false;

// 2. Fetch User Details Securely
$usersDataSet = new UsersDataSet();
$view->user = $usersDataSet->fetchUserByID($emp_id); // Fetches by ID, bypassing password check

// Check if user fetch failed (e.g., account deleted while session was active)
if ($view->user === false) {
    session_destroy();
    header('Location: login.php?error=user_not_found');
    exit;
}


require_once('Views/employer_profile.phtml');