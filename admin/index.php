<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$stmt = $pdo->query("
    SELECT r.*, s.name AS student_name, c.name AS category_name
    FROM report r
    JOIN student s ON r.student_id = s.id
    JOIN category c ON r.category_id = c.id
    ORDER BY
        CASE 
            WHEN r.status = 'menunggu' THEN 1
            WHEN r.status = 'proses' THEN 2
            ELSE 3
        END,
        r.report_date DESC
");
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ReportApp</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold text-primary">ReportApp Admin</span>
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-primary btn-sm">Laporan</a>
            <a href="students.php" class="btn btn-outline-secondary btn-sm">Data Siswa</a>
            <span class="text-muted">
                Welcome, <?= htmlspecialchars($_SESSION['name']) ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h1 class="fw-bold mb-4">Semua Laporan</h1>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Siswa</th>
                            <th>Kategori</th>
                            <th>Laporan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    Belum ada laporan masuk.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td>#<?= $report['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($report['student_name']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($report['category_name']) ?></td>
                                    <td style="max-width: 300px;">
                                        <div class="text-truncate">
                                            <?= htmlspecialchars($report['report']) ?>
                                        </div>
                                    </td>
                                    <td><?= formatDate($report['report_date']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $report['status'] === 'menunggu' ? 'warning' :
                                            ($report['status'] === 'proses' ? 'primary' : 'success')
                                        ?>">
                                            <?= ucfirst($report['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="feedback.php?id=<?= $report['id'] ?>"
                                               class="btn btn-sm btn-<?= $report['status'] === 'selesai' ? 'info' : 'primary' ?>">
                                                <?= $report['status'] === 'selesai' ? 'Detail' : 'Tanggapi' ?>
                                            </a>

                                            <a href="edit.php?id=<?= $report['id'] ?>"
                                               class="btn btn-sm btn-outline-secondary">
                                                Edit
                                            </a>

                                            <a href="delete.php?id=<?= $report['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
