<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$id = $_GET['id'] ?? null;
if (!$id) redirect('index.php');

// Fetch categories for dropdown
$stmt = $pdo->query("SELECT * FROM category");
$categories = $stmt->fetchAll();

// Fetch report details
$stmt = $pdo->prepare("SELECT * FROM report WHERE id = ?");
$stmt->execute([$id]);
$report = $stmt->fetch();

if (!$report) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_text = $_POST['report'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare(
        "UPDATE report SET report = ?, category_id = ?, status = ? WHERE id = ?"
    );
    $stmt->execute([$report_text, $category_id, $status, $id]);

    redirect('index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - Admin</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold text-primary">ReportApp Admin</span>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</nav>

<div class="container my-5" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="fw-bold mb-4">Edit Data Laporan</h1>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= $report['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Isi Laporan (Original)</label>
                    <textarea name="report" rows="8" class="form-control" required><?= htmlspecialchars($report['report']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="menunggu" <?= $report['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="proses" <?= $report['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                        <option value="selesai" <?= $report['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill py-2 fw-semibold">
                        Update Laporan
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary flex-fill py-2 fw-semibold">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
