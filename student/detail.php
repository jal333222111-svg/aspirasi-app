<?php
require_once '../config/database.php';
require_once '../config/functions.php';
checkLogin('student');
$report_id = $_GET['id'] ?? null;
if (!$report_id) redirect('index.php');
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category_name, a.name as admin_name 
    FROM report r 
    JOIN category c ON r.category_id = c.id 
    LEFT JOIN admin a ON r.admin_id = a.id
    WHERE r.id = ? AND r.student_id = ?
");
$stmt->execute([$report_id, $_SESSION['user_id']]);
$report = $stmt->fetch();
if (!$report) redirect('index.php');
$stmt = $pdo->prepare("SELECT * FROM report_picture WHERE report_id = ?");
$stmt->execute([$report_id]);
$pictures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Student</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-primary">ReportApp</span>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </nav>
    <div class="container my-5" style="max-width: 900px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <span class="badge bg-<?= $report['status'] === 'menunggu' ? 'warning' : ($report['status'] === 'proses' ? 'info' : 'success') ?> text-dark mb-2">
                            <?= ucfirst($report['status']) ?>
                        </span>
                        <h1 class="fw-bold mb-1">Laporan #<?= $report['id'] ?></h1>
                        <p class="text-muted mb-0"><?= formatDate($report['report_date']) ?> • <?= htmlspecialchars($report['category_name']) ?></p>
                    </div>
                </div>
                <div class="bg-white border rounded p-3 mb-4">
                    <p class="mb-0" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($report['report'])) ?></p>
                </div>
                <h5 class="fw-bold mb-3">Gambar Lampiran</h5>
                <div class="d-flex gap-3 flex-wrap mb-4">
                    <?php if (empty($pictures)): ?>
                        <p class="text-muted">Tidak ada gambar.</p>
                    <?php else: ?>
                        <?php foreach ($pictures as $pic): ?>
                            <a href="../uploads/<?= $pic['picture'] ?>" target="_blank">
                                <img src="../uploads/<?= $pic['picture'] ?>" class="rounded border shadow-sm"
                                    style="width: 200px; height: 150px; object-fit: cover; transition: transform 0.3s;"
                                    onmouseover="this.style.transform='scale(1.05)'"
                                    onmouseout="this.style.transform='scale(1)'">
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if ($report['feedback']): ?>
                    <hr class="my-4">
                    <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Tanggapan Admin</h5>
                            <small class="text-muted"><?= formatDate($report['feedback_date']) ?></small>
                        </div>
                        <p class="mb-2" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($report['feedback'])) ?></p>
                        <p class="mb-0 text-muted small">Oleh: <?= htmlspecialchars($report['admin_name']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
