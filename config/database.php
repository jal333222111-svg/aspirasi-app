<?php
$host = 'localhost';
$db   = 'ukk_aspirasi';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In a real application, you'd log this and show a generic error message
    // For this task, I'll keep it simple for debugging
    // throw new \PDOException($e->getMessage(), (int)$e->getCode());
    die("Database connection failed: " . $e->getMessage());
}
