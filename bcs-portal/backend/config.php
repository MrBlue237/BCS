<?php
// backend/config.php
declare(strict_types=1);

// Show errors while developing (remove or turn off in production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

// database.sqlite is in the project root: C:\xampp\htdocs\bcs-portal\database.sqlite
$dbFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    exit(
        'Database connection failed: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
    );
}
