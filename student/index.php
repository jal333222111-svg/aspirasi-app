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
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Saya</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background:#f2f4f7;
    font-family:'Segoe UI';
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

/* TITLE */
.page-title {
    color:#0B2D72;
    font-weight:600;
}

/* BUTTON */
.btn-main {
    background:#0B2D72;
    color:#fff;
    border:none;
}

.btn-main:hover {
    background:#09245c;
    color:#fff;
}

.btn-outline-main {
    border:1px solid #0B2D72;
    color:#0B2D72;
}

.btn-outline-main:hover {
    background:#0B2D72;
    color:#fff;
}

/* CARD */
.report-card {
    border-radius:16px;
    border:none;
    transition:.25s;
}

.report-card:hover {
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

/* STATUS */
.status-wait {
    background:#ffc10730;
    color:#856404;
}

.status-process {
    background:#0dcaf030;
    color:#055160;
}

.status-done {
    background:#19875430;
    color:#155724;
}

.badge {
    font-size:12px;
    padding:6px 10px;
    border-radius:20px;
}

/* CATEGORY */
.category-badge {
    background:#0992C220;
    color:#0992C2;
}

/* TEXT LIMIT */
.text-limit {
    max-height:4.5em;
    overflow:hidden;
    line-height:1.5em;
}

/* EMPTY STATE */
.empty-box {
    border-radius:16px;
}

/* RESPONSIVE */
@media(max-width:768px){
    .page-title {
        font-size:18px;
    }
}
</style>
</head>

<body>


<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 " href="#">
           <img src="../assets/img/logo.svg" class="logo-navbar">
        </a>

    <div class="text-white d-flex gap-2 align-items-center">
        <span style="font-size:13px;">
            <?= htmlspecialchars($_SESSION['name']) ?>
        </span>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</div>
</nav>


<!-- CONTENT -->
<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h5 class="page-title">Laporan Saya</h5>

        <a href="create.php" class="btn btn-main">
            + Buat Laporan
        </a>
    </div>

    <div class="row g-4">

        <?php if (empty($reports)): ?>

            <div class="col-12">
                <div class="card empty-box shadow-sm">
                    <div class="card-body text-center py-5 text-muted">
                        Belum ada laporan 😐<br>
                        Coba buat satu, biar sistem ini ada gunanya.
                    </div>
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($reports as $report): ?>

                <div class="col-md-6 col-lg-3">
                    <div class="card report-card h-100 shadow-sm">

                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge <?= getStatusClass($report['status']) ?>">
                                    <?= ucfirst($report['status']) ?>
                                </span>

                                <small class="text-muted">
                                    <?= formatDate($report['report_date']) ?>
                                </small>
                            </div>

                            <span class="badge category-badge mb-2">
                                <?= htmlspecialchars($report['category_name']) ?>
                            </span>

                            <p class="text-limit mb-auto">
                                <?= htmlspecialchars($report['report']) ?>
                            </p>

                            <a href="detail.php?id=<?= $report['id'] ?>" 
                               class="btn btn-outline-main btn-sm mt-3 w-100">
                                Detail
                            </a>

                        </div>

                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>