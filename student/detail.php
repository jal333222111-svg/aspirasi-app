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

function getStatusClass($status) {
    switch ($status) {
        case 'menunggu': return 'status-wait';
        case 'proses': return 'status-process';
        case 'selesai': return 'status-done';
        default: return 'status-wait';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Laporan - Student</title>
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --main-color: #0992C2;
        --main-hover: #0779a1;
    }

    body {
        background-color: #f4f6f9;
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

.status-wait {
    background-color: #ffc107;
    color: #000;
}

    .status-process {
        background-color: #17a2b8;
        color: #fff;
    }

    .status-done {
        background-color: #28a745;
        color: #fff;
    }

    .report-box {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        line-height: 1.7;
        border: 1px solid #eaeaea;
    }

    .img-preview {
        width: 200px;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        transition: 0.3s ease;
    }

    .img-preview:hover {
        transform: scale(1.07);
    }

    .feedback-box {
        background-color: rgba(9,146,194,0.08);
        border: 1px solid rgba(9,146,194,0.25);
        border-radius: 12px;
        padding: 20px;
    }

    .feedback-title {
        color: var(--main-color);
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

    <div class="text-white d-flex gap-2 align-items-center">
        <a href="index.php" class="btn btn-light btn-sm fw-semibold">Kembali</a>
    </div>
</div>
</nav>

<div class="container my-5" style="max-width: 900px;">

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <span class="badge <?= getStatusClass($report['status']) ?> mb-2">
                        <?= ucfirst($report['status']) ?>
                    </span>

                    <h4 class="fw-bold mb-1">
                        Laporan #<?= $report['id'] ?>
                    </h4>

                    <p class="text-muted mb-0">
                        <?= formatDate($report['report_date']) ?> • 
                        <?= htmlspecialchars($report['category_name']) ?>
                    </p>
                </div>
            </div>

            <div class="report-box mb-4">
                <?= nl2br(htmlspecialchars($report['report'])) ?>
            </div>

            <h5 class="fw-bold mb-3">Gambar Lampiran</h5>

            <div class="d-flex gap-3 flex-wrap mb-4">

                <?php if (empty($pictures)): ?>
                    <p class="text-muted">Tidak ada gambar.</p>
                <?php else: ?>
                    <?php foreach ($pictures as $pic): ?>
                        <a href="../uploads/<?= $pic['picture'] ?>" target="_blank">
                            <img src="../uploads/<?= $pic['picture'] ?>" 
                                 class="img-preview shadow-sm border">
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <?php if ($report['feedback']): ?>

                <hr class="my-4">

                <div class="feedback-box">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold feedback-title mb-0">
                            Tanggapan Admin
                        </h5>
                        <small class="text-muted">
                            <?= formatDate($report['feedback_date']) ?>
                        </small>
                    </div>

                    <p class="mb-2">
                        <?= nl2br(htmlspecialchars($report['feedback'])) ?>
                    </p>

                    <p class="mb-0 text-muted small">
                        Oleh: <?= htmlspecialchars($report['admin_name']) ?>
                    </p>

                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
