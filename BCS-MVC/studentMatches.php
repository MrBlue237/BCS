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

$placements = [];
$selectedSort = $_POST['sort_option'] ?? '';
$selectedMatchPercentage = $_POST['match_percentage'] ?? '50';

if (isset($_POST['cancel'])) {
    $placements = $placementsDataSet->fetchAllPosts();
    $selectedSort = '';
    $selectedMatchPercentage = '50';
}
elseif (isset($_POST['apply_search']) && !empty($selectedSort)) {
    $placements = $placementsDataSet->fetchPostsWithSort($selectedSort);
}
else {
    $placements = $placementsDataSet->fetchAllPosts();
}


// --- 2. Calculate Matches and Match Percentage for all fetched placements ---
$placementsWithMatches = [];
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
    $placementsWithMatches[] = $placement;
}
$placements = $placementsWithMatches;

if ($selectedMatchPercentage !== '') {
    $minimumPercentage = intval($selectedMatchPercentage);

    $filteredPlacements = [];
    foreach ($placements as $placement) {
        if ($placement->matchPercentage >= $minimumPercentage) {
            $filteredPlacements[] = $placement;
        }
    }
    $placements = $filteredPlacements;
}


$view->placements = $placements;
$view->selectedSort = $selectedSort; // Pass selected sort to view
$view->selectedMatchPercentage = $selectedMatchPercentage; // Pass selected filter to view

// Render view
require_once("Views/studentListings.phtml");