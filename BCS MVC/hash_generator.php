<?php
// Start session for standard practice, though not strictly needed for this utility
session_start();

// Display errors for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize a standard object to hold data for the view
$view = new stdClass();
$view->pageTitle = 'Password Hash Generator';
$view->plainPassword = '';
$view->hashedPassword = '';

// Check if the form has been submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the password input field exists and is not empty
    if (isset($_POST['password']) && !empty($_POST['password'])) {

        // 1. Get the plain text password from the form
        $plainPassword = $_POST['password'];

        // 2. Generate the secure hash using the recommended PHP function
        // PASSWORD_DEFAULT uses the strongest algorithm available (currently bcrypt)
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // 3. Store results in the $view object to be displayed in the PHTML file
        $view->plainPassword = htmlspecialchars($plainPassword);
        $view->hashedPassword = htmlspecialchars($hashedPassword);

    } else {
        $view->error = 'Please enter a password to hash.';
    }
}

// Load the View file to display the form and results
require_once('Views/hash_generator.phtml');