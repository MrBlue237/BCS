<?php
session_start();
require_once('Models/StudentDataSet.php');
require_once('Models/PlacementsDataSet.php');




$placementsDataSet = new PlacementsDataSet();
$view = new stdClass();
$view->pageTitle = 'Placements';

// Fetch all posts
$placements = $placementsDataSet->fetchAllPosts();

$view->placements = $placements;

// Render view
require_once("Views/browsing.phtml");