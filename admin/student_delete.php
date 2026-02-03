<?php
require_once '../config/database.php';
require_once '../config/functions.php';
checkLogin('admin');
$id = $_GET['id'] ?? null;
if (!$id) redirect('students.php');
try {
    // Delete student (reports will be deleted automatically due to CASCADE)
    $stmt = $pdo->prepare("DELETE FROM student WHERE id = ?");
    $stmt->execute([$id]);
    redirect('students.php?success=deleted');
} catch (Exception $e) {
    redirect('students.php?error=delete_failed');
}