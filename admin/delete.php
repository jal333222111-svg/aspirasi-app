<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('index.php');
}

try {
    $pdo->beginTransaction();

    // Ambil gambar terkait laporan
    $stmt = $pdo->prepare(
        "SELECT picture FROM report_picture WHERE report_id = ?"
    );
    $stmt->execute([$id]);
    $pictures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hapus file gambar dari folder
    foreach ($pictures as $pic) {
        $filePath = '../uploads/' . $pic['picture'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus laporan (report_picture terhapus via ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM report WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    redirect('index.php');

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting report: " . $e->getMessage());
}
