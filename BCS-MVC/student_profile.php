<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Models
require_once('Models/StudentDataSet.php');
require_once('Models/UsersDataSet.php');

// 1. Access Control: Must be logged in AND must be a student
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] ?? '') !== 'student') {
    header('Location: login.php'); // Redirect non-students and not-logged-in users
    exit;
}

$studentId = $_SESSION['user_id'];
$studentDataSet = new StudentDataSet();
$view = new stdClass();
$view->pageTitle = 'Student Profile';
$view->updateSuccess = false;
$view->updateError = false;

// 2. Fetch User Details Securely
$usersDataSet = new UsersDataSet();
$view->user = $usersDataSet->fetchUserByID($studentId); // Fetches by ID, bypassing password check

// Check if user fetch failed (e.g., account deleted while session was active)
if ($view->user === false) {
    session_destroy();
    header('Location: login.php?error=user_not_found');
    exit;
}

// 3. Handle Form Submission (Update Skills)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_skills'])) {

    $skillsToUpdate = [];
    // Process submitted skills (look for skill codes prefixed with 'skill_')
    foreach ($_POST as $key => $value) {
        // Ensure the value is a valid level (1-7)
        if (strpos($key, 'skill_') === 0 && intval($value) >= 1 && intval($value) <= 7) {
            $skillCode = substr($key, 6); // Extract the skill code (e.g., 'PROG')
            $skillsToUpdate[$skillCode] = intval($value);
        }
    }

    if ($studentDataSet->updateStudentSkills($studentId, $skillsToUpdate)) {
        $view->updateSuccess = true;
    } else {
        $view->updateError = true;
    }
}

// 4. Fetch data for the View (This must be done AFTER submission to show updated skills)
$view->allSkills = $studentDataSet->fetchAllSkillsWithStudentStatus($studentId);

require_once('Views/student_profile.phtml');