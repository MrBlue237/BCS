<?php
session_start();
require_once('Models/UsersDataSet.php');

$usersDataSet = new UsersDataSet();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $token = $_POST['token'] ?? '';
    $newPass = $_POST['password'] ?? '';

    $user = $usersDataSet->getUserByToken($token);

    if ($user) {
        $usersDataSet->updatePassword($user->getUserID(), $newPass);
        header("Location: login.php?resetSuccess=1");
        exit;
    } else {
        echo "Invalid or expired link.";
    }
}