<?php
//session_start();
require_once('Models/UsersDataSet.php');

$usersDataSet = new UsersDataSet();
$view = new stdClass();
$view->forgotError = "";
$view->forgotSuccess = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_POST['email'])) {
        $view->forgotError = "Please enter your email.";
    } else {

        $email = trim($_POST['email']);

        // Success message always (prevents enumeration)
        $view->forgotSuccess = "If that email exists, a reset link was sent.";

        // If email exists → create token
        if ($usersDataSet->checkIfEmailExists($email)) {

            $token = bin2hex(random_bytes(32));
            $usersDataSet->storeResetToken($email, $token);

            // send email
            $resetLink = "http://localhost/resetPassword.php?token=$token";
            mail($email, "Reset Password", "Click here: $resetLink");
        }
    }
}

require('Views/forgotPassword.phtml');