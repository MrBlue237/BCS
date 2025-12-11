<?php
session_start();
require_once('Models/UsersDataSet.php');
require_once('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('PHPMailer/Exception.php');
require_once('PHPMailer/PHPMailer.php');
require_once('PHPMailer/SMTP.php');

$usersDataSet = new UsersDataSet();

$view = new stdClass();
$view->forgotError = "";
$view->forgotSuccess = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $view->forgotError = "Please enter your email.";
    } else {
        $user = $usersDataSet->getUserByEmail($email);

        // Always show success message (prevents email enumeration)
        $view->forgotSuccess = "If that email is registered, a reset link has been sent.";

        if ($user) {
            // Generate random token
            $token = bin2hex(random_bytes(32));

            // Hash it before storing in database
            $hashedToken = hash('sha256', $token);
            $usersDataSet->storeResetToken($email, $hashedToken);

            // Send the ORIGINAL token in the email (not the hash)
            $resetLink = "http://vulnerabilities-extradite.poseidon.salford.ac.uk/hackcamp/BCS-MVC/resetPassword.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME; // Your Gmail address
                $mail->Password   = SMTP_PASSWORD; // Paste app password here (no spaces)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;

                $mail->setFrom(SMTP_USERNAME, 'BCS System - Do Not Reply');
                $mail->addAddress($email, $user['name']);

                $mail->isHTML(false);
                $mail->Subject = "Password Reset Request";
                $mail->Body    = "Hi {$user['name']},\n\nClick the link below to reset your password:\n\n$resetLink\n\nThis link expires in 1 hour.\n\nIf you did not request this, ignore this email.";

                $mail->send();
                error_log("Reset email sent successfully to: $email");

            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
                // Don't reveal email sending failure to user (security)
            }
        } else {
            error_log("Reset requested for non-existent email: $email");
        }
    }
}

require('Views/forgotPassword.phtml');