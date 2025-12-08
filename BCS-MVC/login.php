<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once ('config.php');
require_once('Models/UsersDataSet.php');

$view = new stdClass();
$view->pageTitle = 'User Login';
$view->loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        $view->loginError = 'Captcha is required.';
    } else {
        $captcha = $_POST['g-recaptcha-response'];
        $secretKey = RECAPTCHA_SECRET_KEY;
        $ip = $_SERVER['REMOTE_ADDR'];

        $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
        $response = [
            'secret' => $secretKey,
            'response' => $captcha,
            'remoteip' => $ip
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($response)
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ] //temporary remove before uploading to poseidon, as local host doesnt habe SSL certs
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($verifyURL, false, $context);
        $result = json_decode($result);

        if ($result->success !== true) {
            $view->loginError = 'Captcha is required.';
        }
    }

    if (empty($view->loginError)) {
        $usersDataSet = new UsersDataSet();
        $user = $usersDataSet->findUserAndVerifyPassword($email, $password);

        if ($user !== false) {

            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user->getUserID();
            $_SESSION['role'] = $user->getRole();
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['name'] = $user->getName();
            $_SESSION['phone_number'] = $user->getPhoneNumber();
            $_SESSION['postal_address'] = $user->getPostalAddress();
            $_SESSION['cv_file_path'] = $user->getCvFilePath();
        } else {
            $view->loginError = 'Invalid email or password.';
        }
    }
}


// Load the View
require_once('Views/login.phtml');