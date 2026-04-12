<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$id = $_GET['id'] ?? null;
if (!$id) redirect('index.php');

// Fetch categories
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
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Laporan</title>
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #eef3f7;
    font-family: 'Segoe UI', sans-serif;
}

.navbar {
    background-color: #3aa6b9 !important;
}

.navbar-brand span {
    font-size: 18px;
}

.logo-navbar {
    height: 45px;
    width: auto;
}

.card {
    border-radius: 18px;
    border: none;
}

.card-body {
    border-radius: 18px;
}

h1 {
    font-size: 22px;
    color: #0992C2;
}

.form-control,
.form-select {
    border-radius: 10px;
    padding: 10px;
}

textarea.form-control {
    resize: none;
}

.form-control:focus,
.form-select:focus {
    border-color: #0992C2;
    box-shadow: 0 0 0 0.2rem rgba(9,146,194,0.25);
}

.btn-primary {
    background-color: #0d3b66;
    border: none;
    border-radius: 10px;
}

.btn-primary:hover {
    background-color: #0b2f52;
}

.btn-outline-secondary {
    border-radius: 10px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 " href="#">
            <img src="../assets/img/logo.svg" alt="Logo" class="logo-navbar">
        </a>

        <!-- BUTTON -->
        <a href="index.php" class="btn btn-light btn-sm fw-semibold">
            Kembali
        </a>

    </div>
</nav>

<!-- FORM -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
    <div style="width: 100%; max-width: 650px;">

        <div class="card shadow">
            <div class="card-body p-4">

                <h1 class="fw-bold mb-4">Edit Data Laporan</h1>

                <form method="POST">

                    <!-- KATEGORI -->
                    <div class="mb-4">
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

                    <!-- ISI LAPORAN -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Isi Laporan</label>
                        <textarea name="report" rows="6" class="form-control" required><?= htmlspecialchars($report['report']) ?></textarea>
                    </div>

                    <!-- STATUS -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="menunggu" <?= $report['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="proses" <?= $report['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                            <option value="selesai" <?= $report['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>

                    <!-- BUTTON -->
                    <div class="d-flex gap-3 mt-4">
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
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>