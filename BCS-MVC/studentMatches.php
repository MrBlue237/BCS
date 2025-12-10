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
$selectedSort = $_POST['sort_option'] ?? 'match_high';
$selectedMatchPercentage = $_POST['match_percentage'] ?? '50';

if (isset($_POST['cancel'])) {
    $placements = $placementsDataSet->fetchAllPosts();
    $selectedSort = 'match_high';
    $selectedMatchPercentage = '50';
}
elseif (isset($_POST['apply_search']) && !empty($selectedSort) && $selectedSort !== 'match_high') {
    $placements = $placementsDataSet->fetchPostsWithSort($selectedSort);
}
else {
    $placements = $placementsDataSet->fetchAllPosts();
}

$placementsWithMatches = [];
foreach ($placements as $placement) {
    $placementId = $placement->getPlacementID();

    $counts = $studentDataSet->getSkillMatchCounts($studentId, $placementId);

    $placement->matchedCount = $counts['matched_count'];
    $placement->totalRequired = $counts['total_required'];

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

if ($selectedSort === 'match_high') {
    usort($placements, function($a, $b) {
        if ($a->matchPercentage == $b->matchPercentage) {
            return 0;
        }
        return ($a->matchPercentage < $b->matchPercentage) ? 1 : -1;
    });
}


$view->placements = $placements;
$view->selectedSort = $selectedSort; // Pass selected sort to view
$view->selectedMatchPercentage = $selectedMatchPercentage; // Pass selected filter to view

// Render view
require_once("Views/studentListings.phtml");