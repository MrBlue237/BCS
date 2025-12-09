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

$studentId = $_SESSION['user_id'];

$placementsDataSet = new PlacementsDataSet();
$studentDataSet = new StudentDataSet();
$view = new stdClass();
$view->pageTitle = 'Matched Placements';

// Fetch all posts
$placements = $placementsDataSet->fetchAllPosts();

// Calculate matches and add the count to each placement object
foreach ($placements as $placement) {
    $placementId = $placement->getPlacementID();

    // Calculate the match count using the new Model method
    $counts = $studentDataSet->getSkillMatchCounts($studentId, $placementId);

    // Add both properties to the placement object for the view
    $placement->matchedCount = $counts['matched_count'];
    $placement->totalRequired = $counts['total_required'];

    // Calculate percentage
    if ($placement->totalRequired > 0) {
        $percentage = round(($placement->matchedCount / $placement->totalRequired) * 100);
    } else {
        $percentage = 0;
    }
    $placement->matchPercentage = $percentage;
}

$view->placements = $placements;

// Render view
require_once("Views/studentListings.phtml");