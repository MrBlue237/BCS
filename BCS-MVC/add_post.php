<?php
/**
 * @file edit_pet.php
 * @author Mohammad Bahaauddeen
 * @version 1.0.0
 * @package Controllers
 * @copyright 2025 University of Salford
 * * Controller responsible for editing an existing pet record.
 * Handles the processing of form submissions,
 * managing image uploads, updating the record.
 */

// Start session if not already active,
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load the necessary Model class for database operations.
require_once('Models/PlacementsDataSet.php'); // Your dataset class

//make view class
$view = new stdClass();
$view->pageTitle = 'Create A New Post';
// create a new sighting dataset object that we can generate data from
$placementsDataSet = new PlacementsDataSet();

/**
 * Handles the 'Add Post' submission from the modal form.
 */
if(isset($_POST['add_post']))// if the user has pressed the Add post button
{
    /**
     * @param string $name name of pet
     * @param string $species species of pet
     * @param string $breed breed of pet
     * @param string $color color of pet
     * @param string $image_url The filename of the pet's photo.
     * @param string $description short description of pet
     * @param string $date_reported Current date in Y-m-d format.
     * @param int $user_id The ID of the user who reported the pet.
     */

    //retrieve and set all the fields after submission
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $start_date = trim($_POST['start']);
    //$date_reported = date("Y-m-d");
    $end_date = trim($_POST['end']);
    $employer_id = $_SESSION['user_id'];

    //handle if any required area was empty
    if(empty($title)
        || empty($description)
        || empty($salary)
        || empty($start_date)
        || empty($end_date))
    {
        echo "All fields are required";
    }
    else{
        //insert new pet record into pets database
        $placementsDataSet->insertPlacement($employer_id, $title, $description,$salary, $location, $start_date, $end_date);
        //display to user a simple success message
        $_SESSION['placements_success'] = 'Success: Your Pet is successfully added!';
        //reload page to show updated version
        header('Location: my_posts.php');
        exit;
    }


}

// Include the form view
require_once('Views/add_post.phtml');




