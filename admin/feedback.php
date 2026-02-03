<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$report_id = $_GET['id'] ?? null;
if (!$report_id) redirect('index.php');

// Fetch report details
$stmt = $pdo->prepare("
    SELECT r.*, s.name AS student_name, s.nisn, s.class, c.name AS category_name
    FROM report r
    JOIN student s ON r.student_id = s.id
    JOIN category c ON r.category_id = c.id
    WHERE r.id = ?
");
$stmt->execute([$report_id]);
$report = $stmt->fetch();

if (!$report) redirect('index.php');

// Fetch pictures
$stmt = $pdo->prepare("SELECT * FROM report_picture WHERE report_id = ?");
$stmt->execute([$report_id]);
$pictures = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = $_POST['feedback'];
    $status = $_POST['status'];
    $admin_id = $_SESSION['user_id'];
    $date = date('Y-m-d');

    $stmt = $pdo->prepare(
        "UPDATE report 
         SET feedback = ?, status = ?, admin_id = ?, feedback_date = ? 
         WHERE id = ?"
    );
    $stmt->execute([$feedback, $status, $admin_id, $date, $report_id]);

    redirect('index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Laporan - Admin</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold text-primary">ReportApp Admin</span>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row g-4">

        <!-- Left: Report Info -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-4">Detail Laporan</h2>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase mb-1">Siswa</div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($report['student_name']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase mb-1">Kelas</div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($report['class'] ?? '-') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase mb-1">Kategori</div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($report['category_name']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase mb-1">Tanggal</div>
                                    <div class="fw-semibold">
                                        <?= formatDate($report['report_date']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-muted small text-uppercase mb-2">Isi Laporan</div>
                        <div class="card bg-white border">
                            <div class="card-body">
                                <p class="mb-0">
                                    <?= nl2br(htmlspecialchars($report['report'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-muted small text-uppercase mb-2">Gambar Lampiran</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if (empty($pictures)): ?>
                                <p class="text-muted small">Tidak ada gambar.</p>
                            <?php else: ?>
                                <?php foreach ($pictures as $pic): ?>
                                    <a href="../uploads/<?= $pic['picture'] ?>" target="_blank">
                                        <img src="../uploads/<?= $pic['picture'] ?>"
                                             class="rounded border"
                                             style="width: 120px; height: 100px; object-fit: cover;">
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right: Feedback Form -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-4">Berikan Tanggapan</h2>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Laporan</label>
                            <select name="status" class="form-select" required>
                                <option value="menunggu" <?= $report['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="proses" <?= $report['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                                <option value="selesai" <?= $report['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggapan Anda</label>
                            <textarea name="feedback"
                                      rows="10"
                                      class="form-control"
                                      required
                                      placeholder="Berikan tanggapan, instruksi, atau penyelesaian terhadap laporan ini"><?= htmlspecialchars($report['feedback'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            Simpan Tanggapan
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
