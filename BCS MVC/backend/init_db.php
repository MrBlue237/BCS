<?php
// backend/init_db.php
require __DIR__ . '/config.php';

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('student','employer','admin')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
SQL;

try {
    $pdo->exec($sql);
    echo 'Database and users table created successfully.';
} catch (PDOException $e) {
    echo 'Error creating table: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
