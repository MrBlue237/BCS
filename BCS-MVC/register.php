<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

const CV_UPLOAD_DIR = 'cv_files/';
if (!is_dir(CV_UPLOAD_DIR)) {
    mkdir(CV_UPLOAD_DIR, 0777, true);
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once('Models/UsersDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Register Account';
$view->errors = [];
$view->success = '';
$view->formData = [];
$view->nameLabel = 'Full Name *';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'name' => trim($_POST['name'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'postal_address' => trim($_POST['postal_address'] ?? ''),
        'role' => $_POST['role'] ?? '',
        'cv_file_path' => null
    ];
    $view->formData = $formData;

    if ($formData['role'] === 'employer') {
        $view->nameLabel = 'Company Name *';
    } else {
        $view->nameLabel = 'Full Name *';
    }
    if ($formData['role'] === 'student' && isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cv_file'];
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
            $targetPath = CV_UPLOAD_DIR . $safeFileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $formData['cv_file_path'] = $targetPath;
                $view->formData['cv_file_path'] = $targetPath;
            } else {
                $view->errors[] = 'Failed to save CV file.';
            }
        }
    }

    if (!empty($formData['password'])) {

        if (empty($formData['name'])) { $view->errors[] = $view->nameLabel . ' is required.'; }
        if (empty($formData['phone_number'])) { $view->errors[] = 'Phone number is required.'; }
        if (empty($formData['postal_address'])) { $view->errors[] = 'Postal address is required.'; }

        if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $view->errors[] = 'A valid email address is required.';
        }
        if (strlen($formData['password']) < 6) {
            $view->errors[] = 'Password must be at least 6 characters long.';
        }
        if ($formData['password'] !== $formData['password_confirm']) {
            $view->errors[] = 'Passwords do not match.';
        }
        if ($formData['role'] !== 'student' && $formData['role'] !== 'employer') {
            $view->errors[] = 'Please select either Student or Employer role.';
        }

        if (empty($view->errors)) {
            $usersDataSet = new UsersDataSet();

            if ($usersDataSet->checkIfEmailExists($formData['email'])) {
                $view->errors[] = 'This email is already registered.';
            } else {
                $success = $usersDataSet->registerUser($formData);

                if ($success) {
                    header('Location: login.php?registered=true');
                    exit;
                } else {
                    if ($formData['cv_file_path'] && file_exists($formData['cv_file_path'])) {
                        unlink($formData['cv_file_path']);
                    }
                    $view->errors[] = 'Database error: Could not register user.';
                }
            }
        }
    }
}

require_once('Views/register.phtml');