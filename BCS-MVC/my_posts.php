<?php
/**
 * @file pets.php
 * @author Mohammad Bahaauddeen
 * @version 1.0.0
 * @package Controllers
 * @copyright 2025 University of Salford
 * * Controller for managing and displaying pet records.
 * Handles fetching, searching, filtering, adding, deleting, and status changes for pets.
 */

// Start session if not already started. To have the correct user state (e.g., logged in).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// load required classes
require_once('Models/PlacementsDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'My Posts';

//add activate option

// create a new student dataset object that we can generate data from
$placementsDataSet = new PlacementsDataSet();
// Fetch all pets for initial display
$view->placementsDataSet = $placementsDataSet->fetchAllPosts();

// send a results count to the view to show how many results were retrieved
if (count($view->placementsDataSet) == 0)
{
    $view->dbMessage = "No results";
}
else
{
    $view->dbMessage = count($view->placementsDataSet) . " result(s)";
}

// include the view
require_once('Views/my_posts.phtml');