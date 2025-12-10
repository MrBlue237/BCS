<?php
session_start();

require_once('Models/StudentDataSet.php');

$studentDataSet = new StudentDataSet();
$view = new stdClass();
$view->pageTitle = "Matched Students";

if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header('Location: index.php');
    exit;
}

if (!isset($_POST['post_id'])) {
    header('Location: my_posts.php');
    exit;
}

$placementId = $_POST['post_id'];

$view->matchedStudents = $studentDataSet->getMatchedStudentsForPlacement($placementId);

require_once("Views/display_matches.phtml");
