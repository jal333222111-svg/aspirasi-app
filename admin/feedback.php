<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$report_id = $_GET['id'] ?? null;
if (!$report_id) {
    echo "ID tidak ditemukan";
    exit;
}

// Fetch report
$stmt = $pdo->prepare("
    SELECT r.*, s.name AS student_name, s.class, c.name AS category_name
    FROM report r
    JOIN student s ON r.student_id = s.id
    JOIN category c ON r.category_id = c.id
    WHERE r.id = ?
");
$stmt->execute([$report_id]);
$report = $stmt->fetch();

// VALIDASI DATA
if (!$report) {
    echo "Data laporan tidak ditemukan";
    exit;
}

// Fetch gambar
$stmt = $pdo->prepare("SELECT * FROM report_picture WHERE report_id = ?");
$stmt->execute([$report_id]);
$pictures = $stmt->fetchAll();

// SIMPAN FEEDBACK
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = $_POST['feedback'];
    $status = $_POST['status'];
    $admin_id = $_SESSION['user_id'];
    $date = date('Y-m-d');

    $stmt = $pdo->prepare("
        UPDATE report 
        SET feedback = ?, status = ?, admin_id = ?, feedback_date = ?
        WHERE id = ?
    ");
    $stmt->execute([$feedback, $status, $admin_id, $date, $report_id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback Laporan</title>
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

.title {
    color: #0992C2;
    font-weight: bold;
}

/* BOX KECIL */
.input-box {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 8px 12px;
    background: #f8f9fa;
    font-size: 14px;
}

.label-text {
    font-size: 13px;
    margin-bottom: 4px;
}

/* FORM */
.form-control, .form-select {
    border-radius: 10px;
}

/* BUTTON */
.btn-primary {
    background-color: #0d3b66;
    border: none;
    border-radius: 10px;
}

.btn-primary:hover {
    background-color: #0b2f52;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .logo-navbar {
        height: 35px;
    }
}
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 " href="#">
            <img src="../assets/img/logo.svg" alt="Logo" class="logo-navbar">
        </a>

    <div class="text-white d-flex gap-2 align-items-center">
        <a href="students.php" class="btn btn-light btn-sm fw-semibold">Kembali</a>
    </div>
</div>
</nav>

<!-- CONTENT -->
<div class="container my-5">
<div class="row g-4">

<!-- KIRI -->
<div class="col-lg-6">
<div class="card shadow p-4">

<h5 class="title mb-4">Detail Laporan</h5>

<div class="row g-3">

<div class="col-6">
<div class="label-text">Siswa</div>
<div class="input-box"><?= htmlspecialchars($report['student_name'] ?? '-') ?></div>
</div>

<div class="col-6">
<div class="label-text">Kelas</div>
<div class="input-box"><?= htmlspecialchars($report['class'] ?? '-') ?></div>
</div>

<div class="col-6">
<div class="label-text">Kategori</div>
<div class="input-box"><?= htmlspecialchars($report['category_name'] ?? '-') ?></div>
</div>

<div class="col-6">
<div class="label-text">Tanggal</div>
<div class="input-box">
<?= date('d M Y', strtotime($report['report_date'])) ?>
</div>
</div>

<div class="col-12">
<div class="label-text">Isi laporan</div>
<div class="input-box">
<?= nl2br(htmlspecialchars($report['report'] ?? '-')) ?>
</div>
</div>

<div class="col-12">
<div class="label-text">Gambar lampiran</div>
<div class="input-box d-flex gap-2 flex-wrap">

<?php if (empty($pictures)): ?>
    <small class="text-muted">Tidak ada gambar</small>
<?php else: ?>
    <?php foreach ($pictures as $pic): ?>
        <img src="../uploads/<?= $pic['picture'] ?>" 
        style="width:120px;height:90px;object-fit:cover;border-radius:8px;">
    <?php endforeach; ?>
<?php endif; ?>

</div>
</div>

</div>

</div>
</div>

<!-- KANAN -->
<div class="col-lg-6">
<div class="card shadow p-4">

<h5 class="title mb-4">Berikan Tanggapan</h5>

<form method="POST">

<div class="mb-3">
<label class="label-text">Status</label>
<select name="status" class="form-select" required>
<option value="">Pilih status</option>
<option value="menunggu" <?= $report['status']=='menunggu'?'selected':'' ?>>Menunggu</option>
<option value="proses" <?= $report['status']=='proses'?'selected':'' ?>>Proses</option>
<option value="selesai" <?= $report['status']=='selesai'?'selected':'' ?>>Selesai</option>
</select>
</div>

<div class="mb-3">
<label class="label-text">Tanggapan</label>
<textarea name="feedback" rows="6" class="form-control" required><?= htmlspecialchars($report['feedback'] ?? '') ?></textarea>
</div>

<button class="btn btn-primary w-100">Simpan tanggapan</button>

</form>

</div>
</div>

</div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>