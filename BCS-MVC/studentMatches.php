<?php
session_start();
require_once('Models/StudentDataSet.php');
require_once('Models/UsersDataSet.php');
require_once('Models/PlacementsDataSet.php');

// If student not logged in, redirect
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] ?? '') !== 'student') {
    header('Location: login.php'); // Redirect non-students and not-logged-in users
    exit;
}


$placementsDataSet = new PlacementsDataSet();
$placements = $placementsDataSet->fetchAllPosts();

// Render view
require_once("Views/studentListings.phtml");
