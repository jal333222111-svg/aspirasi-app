<?php
require_once '../config/database.php';
require_once '../config/functions.php';
checkLogin('student');
$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category_name 
    FROM report r 
    JOIN category c ON r.category_id = c.id 
    WHERE r.student_id = ? 
    ORDER BY r.report_date DESC
");
$stmt->execute([$student_id]);
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - Student</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-primary">ReportApp</span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="../auth/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">Laporan Saya</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Buat Laporan
            </a>
        </div>
        <div class="row g-4">
            <?php if (empty($reports)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5 text-muted">
                            Belum ada laporan. Silahkan buat laporan baru.
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-<?= $report['status'] === 'menunggu' ? 'warning' : ($report['status'] === 'proses' ? 'info' : 'success') ?> text-dark">
                                            <?= ucfirst($report['status']) ?>
                                        </span>
                                        <small class="text-muted"><?= formatDate($report['report_date']) ?></small>
                                    </div>
                                    <span class="badge bg-light text-primary border">
                                        <?= htmlspecialchars($report['category_name']) ?>
                                    </span>
                                </div>
                                <p class="card-text mb-auto" style="max-height: 4.5em; line-height: 1.5em; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($report['report']) ?>
                                </p>
                                <a href="detail.php?id=<?= $report['id'] ?>" class="btn btn-outline-primary btn-sm mt-3 w-100">Detail</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>