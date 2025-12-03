<?php
// backend/login.php
declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.html');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || $role === '') {
    showLoginError('Please fill in all fields with valid data.');
}

// Look up user by email
$stmt = $pdo->prepare(
    'SELECT id, name, email, password_hash, role
     FROM users
     WHERE email = :email
     LIMIT 1'
);
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// User not found
if (!$user) {
    showLoginError('No account found with that email address.');
}

// Role mismatch
if ($user['role'] !== $role) {
    showLoginError('This account type does not match the selected role.');
}

// Password check
if (!password_verify($password, $user['password_hash'])) {
    showLoginError('Incorrect password. Please try again.');
}

// Success: create session & redirect
session_regenerate_id(true);
$_SESSION['user_id']    = (int) $user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role'];

switch ($user['role']) {
    case 'student':
        header('Location: ../student_dashboard.php');
        break;
    case 'employer':
        header('Location: ../employer_dashboard.php'); // to be created later
        break;
    case 'admin':
        header('Location: ../admin_dashboard.php'); // to be created later
        break;
    default:
        header('Location: ../public/login.html');
}
exit;

/**
 * Render a simple styled error screen and stop.
 */
function showLoginError(string $message): void
{
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
    echo '<title>Login error</title>';
    echo '<link rel="stylesheet" href="../public/css/main.css">';
    echo '</head><body class="page page--auth">';
    echo '<main class="auth"><section class="auth-card">';
    echo '<h1 class="auth-card__title">Unable to sign you in</h1>';
    echo '<p class="form__error" style="margin-top:0.75rem;">'
        . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
        . '</p>';
    echo '<p class="form__links" style="margin-top:1.25rem;">';
    echo '<a class="link" href="../public/login.html">Back to login</a>';
    echo '</p>';
    echo '</section></main></body></html>';
    exit;
}
