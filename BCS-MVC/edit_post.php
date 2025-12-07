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
$view->pageTitle = 'Edit Post Details';
// create a new sighting dataset object that we can generate data from
$placementDataSet = new PlacementsDataSet();

/**
 * Check if the ID is present for the page load.
 * Triggered when clicking the 'Edit' button on the main pets.php page.
 */
if (isset($_POST['id'])){
    /**
     * @param int $pet_id id of current pet to edit
     */
    $post_id = $_POST['id'];
    // Fetch the specific pet's data with its id
    $placementData = $placementDataSet->fetchPostById($post_id);
    $view->placement = $placementData[0];
    }

/**
 * Check updating the placement record submitted .
 * Triggered by the 'Save Changes' button.
 */
else if (isset($_POST['update_post'])) {
// Handle the form submission
    /**
     * @param string $name name of current pet
     * @param string $species species of current pet
     * @param string $breed breed of current pet
     * @param string $color color of current pet
     * @param string $image_url The filename (new or old).
     * @param string $description description of current pet
     * @param string $petData data of current pet
     */
    $post_id = $_POST['post_id']; // ID is passed via a hidden field
    $title = $_POST['title'];
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $start = trim($_POST['start']);
    $end = trim($_POST['end']);
    $description = trim($_POST['description']);
    $deadline = trim($_POST['deadline']);
    $about = trim($_POST['about']);
    $offer = trim($_POST['offer']);
// Fetch the existing pet data to get the current image URL
// $placementData = $placementDataSet->fetchPetById($pet_id);
// $image_url = $placementData[0]->getPhotoUrl();//set as old value
//
// if (isset($_FILES['petphoto']) && $_FILES['petphoto']['error'] === UPLOAD_ERR_OK) {
//
// $uploadDir = __DIR__ . '/images/'; // uses __DIR__ which is the full path
// $fileTmpPath = $_FILES['petphoto']['tmp_name'];
// $fileName = basename($_FILES['petphoto']['name']);
// $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
// $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
//
// if (in_array($fileExtension, $allowedExtensions)) {
// $newFileName = uniqid('pet_', true) . '.' . $fileExtension;
// $destination = $uploadDir . $newFileName;
//
// if (move_uploaded_file($fileTmpPath, $destination)) {
//
// $image_url = $newFileName;
// // using this message variable to show updates in the view
// $view->message = "Pet photo changed successfully! ";
// } else {
// $view->message = "Error moving uploaded file.";
// }
// } else {
// $view->message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
// }
// }

    //make sure required fields are full
    if (empty($title) || empty($salary) || empty($location) || empty($start) || empty($end) || empty($description) || empty($deadline) || empty($about) || empty($offer))
    {
        // Re-load the data and show an error message
        $view->error = "All fields are required.";
        // re-display the form with an error
        // $postDataArray = $placementDataSet->fetchPetById($post_id);
    }
    else {
        // Call update function
        $placementDataSet->updateData($post_id, $title, $salary, $location, $start,$end, $description, $deadline, $about, $offer);
        // Redirect back to the main pets page on success
        header('Location: my_posts.php');
        exit;
    }
}

// Include the form view
require_once('Views/edit_post.phtml');