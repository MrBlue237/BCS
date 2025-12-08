<?php
/**
 * Controller responsible for creating a new placement record.
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load the necessary Model class for database operations.
require_once('Models/PlacementsDataSet.php');

// Access Control: Must be logged in AND must be an employer
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header('Location: index.php');
    exit;
}

// make view class
$view = new stdClass();
$view->pageTitle = 'Create A New Post';
$view->postErrors = []; // Use a specific variable for errors
$view->formData = []; // Store input data to repopulate form
$placementsDataSet = new PlacementsDataSet();

// Fetch all available SFIA skills for the form
$view->availableSkills = $placementsDataSet->fetchAvailableSkills();

/**
 * Handles the 'Add Post' submission.
 */
if(isset($_POST['add_post']))
{
    // 1. Retrieve and clean fields
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $start_date = trim($_POST['start'] ?? '');
    $end_date = trim($_POST['end'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $date_posted = date('Y-m-d');
    $about_company = trim($_POST['about'] ?? '');
    $offers = trim($_POST['offers'] ?? '');
    $employer_id = $_SESSION['user_id'];
    $submitted_skills = $_POST['skills'] ?? [];

    $view->formData = $_POST; // Store all submitted data

    // 2. Validation Checks
    if(empty($title) || empty($description) || empty($salary) || empty($location) || empty($start_date) || empty($end_date) || empty($about_company) || empty($offers))
    {
        $view->postErrors[] = "All fields are required.";
    }

    // Validate salary is numeric
    if(!is_numeric($salary) || $salary < 0) {
        $view->postErrors[] = "Salary must be a positive number.";
    }

    // Validate date format (assumes YYYY-MM-DD from input type="date")
    $startDateObj = DateTime::createFromFormat('Y-m-d', $start_date);
    $endDateObj = DateTime::createFromFormat('Y-m-d', $end_date);
    $deadlineObj = DateTime::createFromFormat('Y-m-d', $deadline);

    if ($startDateObj === false || $startDateObj->format('Y-m-d') !== $start_date) {
        $view->postErrors[] = "Start Date must be a valid date in YYYY-MM-DD format.";
    }
    if ($endDateObj === false || $endDateObj->format('Y-m-d') !== $end_date) {
        $view->postErrors[] = "End Date must be a valid date in YYYY-MM-DD format.";
    } elseif ($startDateObj > $endDateObj) {
        $view->postErrors[] = "Start Date cannot be after the End Date.";
    }

    if (empty($deadline)) {
        $view->postErrors[] = "The application deadline is required.";
    } elseif ($deadlineObj === false || $deadlineObj->format('Y-m-d') !== $deadline) {
        $view->postErrors[] = "Deadline must be a valid date in YYYY-MM-DD format.";
    } elseif ($deadlineObj > $startDateObj) {
        $view->postErrors[] = "The Deadline cannot be after the Start Date.";
    }

    // 3. Process insertion only if there are NO errors
    if(empty($view->postErrors)){

        // Step 3a: Insert the main placement record and get its new ID
        $placementId = $placementsDataSet->insertPlacement($employer_id, $title, $description, $salary, $location, $start_date, $end_date, $deadline, $date_posted, $about_company, $offers);

        if ($placementId) {
            // Step 3b: Insert the required skills using the new placement ID
            $success_skills = $placementsDataSet->insertRequiredSkills($placementId, $submitted_skills);

            if ($success_skills) {
                $_SESSION['placements_success'] = 'Success: Placement and required skills added!';
                header('Location: my_posts.php');
                exit;
            } else {
                $view->postErrors[] = "Placement added, but failed to save skill requirements.";
            }

        } else {
            $view->postErrors[] = "Database error: Failed to insert placement record.";
        }
    }
}

// Include the form view
require_once('Views/add_post.phtml');