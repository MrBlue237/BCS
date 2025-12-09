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
require_once('Models/UsersDataSet.php'); // Your dataset class
//make view class
$view = new stdClass();
$view->pageTitle = 'Edit Details';
// create a new sighting dataset object that we can generate data from
$usersDataSet = new UsersDataSet();


/**
 * Check updating the placement record submitted .
 * Triggered by the 'Save Changes' button.
 */
if (isset($_POST['update_profile'])) {
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
    $user_id = $_POST['user_id']; // ID is passed via a hidden field
    $name = trim($_POST['name']);
    $number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);

    $view->users = $usersDataSet->fetchUserByID($user_id);//set as old value
    $cv = $view->users->getCvFilePath(); // correct getter

    if (isset($_FILES['cv_file_upload']) && $_FILES['cv_file_upload']['error'] === UPLOAD_ERR_OK) {


        $file = $_FILES['cv_file_upload'];
        $allowedExtension = 'pdf';

        $fileInfo = pathinfo($file['name']);
        $fileExtension = strtolower($fileInfo['extension'] ?? '');

        if ($fileExtension !== $allowedExtension) {
            $view->errors[] = 'CV must be a PDF file. Invalid file extension submitted.';
        } elseif ($file['type'] !== 'application/pdf') {
            $view->errors[] = 'CV file type is invalid (Browser reported: ' . $file['type'] . '). Must be application/pdf.';
        }
        else {
            $safeFileName = uniqid('cv_', true) . '.' . $allowedExtension;
            define('CV_UPLOAD_DIR', __DIR__ . '/BCS-MVC/cv_files/');
            $targetPath = CV_UPLOAD_DIR . $safeFileName;
            $cv = $targetPath;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $formData['cv_file_upload'] = $targetPath;
                $view->formData['cv_file_upload'] = $targetPath;
            } else {
                $view->errors[] = 'Failed to save CV file.';
            }
        }
    }


    //make sure required fields are full
    if (empty($name) || empty($number) || empty($address) || empty($email))
    {
        // Re-load the data and show an error message
        $view->error = "All fields are required.";
        // re-display the form with an error
        // $postDataArray = $placementDataSet->fetchPetById($post_id);
    }
    else {
        // Call update function
        $usersDataSet->updateUsersData($name, $number, $address, $cv, $email,$user_id);
        // Redirect back to the main pets page on success
        if (isset($_SESSION['logged_in']) && ($_SESSION['role'] ?? '') === 'student'){
            header('Location: student_profile.php');
        }
        else{
            header('Location: employer_profile.php');
        }
        exit;
    }
}

/**
 * Check if the ID is present for the page load.
 * Triggered when clicking the 'Edit' button on the main pets.php page.
 */
else if (isset($_POST['user_id'])){
    /**
     * @param int $pet_id id of current pet to edit
     */
    $user_id = $_POST['user_id'];
    $view->users = $usersDataSet->fetchUserByID($user_id);
}


// Include the form view
require_once('Views/edit_profile.phtml');