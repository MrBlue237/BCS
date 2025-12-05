<?php
// backend/register_student.php
declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Direct access: send back to registration form
    header('Location: ../public/register_student.html');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['password_confirm'] ?? '';

$errors = [];

// Basic validation (mirrors front-end rules)
if ($name === '') {
    $errors[] = 'Name is required.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email is required.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if ($password !== $confirm) {
    $errors[] = 'Passwords do not match.';
}

if ($errors) {
    // Simple error display (fine for internal/admin tools and student projects)
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Registration error</title>';
    echo '<link rel="stylesheet" href="../public/css/main.css"></head><body class="page page--auth">';
    echo '<main class="auth"><section class="auth-card">';
    echo '<h1 class="auth-card__title">Registration problem</h1>';
    echo '<ul class="form__error" style="list-style: disc; padding-left: 1.2rem;">';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    echo '<p class="form__links" style="margin-top:1rem;"><a class="link" href="../public/register_student.html">Go back to registration</a></p>';
    echo '</section></main></body></html>';
    exit;
}

// Hash password securely
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash, role)
         VALUES (:name, :email, :password_hash, :role)'
    );
    $stmt->execute([
        ':name'          => $name,
        ':email'         => $email,
        ':password_hash' => $passwordHash,
        ':role'          => 'student',
    ]);

    // Redirect to login page on success
    header('Location: ../public/login.html?registered=1');
    exit;
} catch (PDOException $e) {
    // Handle duplicate email and other DB errors
    if ($e->getCode() === '23000') {
        $message = 'That email address is already registered. You can log in instead.';
    } else {
        $message = 'Unexpected error: ' . $e->getMessage();
    }

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Registration error</title>';
    echo '<link rel="stylesheet" href="../public/css/main.css"></head><body class="page page--auth">';
    echo '<main class="auth"><section class="auth-card">';
    echo '<h1 class="auth-card__title">Registration problem</h1>';
    echo '<p class="form__error">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p class="form__links" style="margin-top:1rem;">';
    echo '<a class="link" href="../public/register_student.html">Back to registration</a> · ';
    echo '<a class="link" href="../public/login.html">Go to login</a>';
    echo '</p>';
    echo '</section></main></body></html>';
    exit;
}
