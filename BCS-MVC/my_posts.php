<?php
/**
 * Controller for managing and displaying an employer's placement records.
 */

// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// load required classes
require_once('Models/PlacementsDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'My Posts';

// Access Control: Must be logged in AND must be an employer
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header('Location: index.php');
    exit;
}

$employerId = $_SESSION['user_id'];

$placementsDataSet = new PlacementsDataSet();

// Fetch ONLY posts belonging to the logged-in employer
$view->placementsDataSet = $placementsDataSet->fetchPostsByEmployerID($employerId);

// Send a results count to the view
if (count($view->placementsDataSet) == 0)
{
    $view->dbMessage = "No results";
}
else
{
    $view->dbMessage = count($view->placementsDataSet) . " result(s)";
}

// Check for success message from add_post.php
$view->success = $_SESSION['placements_success'] ?? null;
unset($_SESSION['placements_success']);

// include the view
require_once('Views/my_posts.phtml');