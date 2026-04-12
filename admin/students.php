<?php
require_once '../config/database.php';
require_once '../config/functions.php';
checkLogin('admin');

// Get all students
$stmt = $pdo->query("SELECT * FROM student ORDER BY name ASC");
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- CUSTOM THEME -->
    <style>
        :root {
            --bs-primary: #0992C2;
            --bs-primary-rgb: 9, 146, 194;
        }

        body {
            background-color: #f4f8fb;
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

        .btn-primary {
            background-color: #0992C2;
            border-color: #0992C2;
        }

        .btn-primary:hover {
            background-color: #077aa1;
            border-color: #077aa1;
        }

        .btn-outline-primary {
            color: #0992C2;
            border-color: #0992C2;
        }

        .btn-outline-primary:hover {
            background-color: #0992C2;
            border-color: #0992C2;
            color: #fff;
        }

        .text-primary {
            color: #0992C2 !important;
        }

        .card {
            border-radius: 12px;
        }

        .table thead {
            background-color: #e6f4fa;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(9, 146, 194, 0.05);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 " href="#">
           <img src="../assets/img/logo.svg" class="logo-navbar">
        </a>

    <div class="text-white d-flex gap-2 align-items-center">
        <span style="font-size:13px;">
            Welcome <?= htmlspecialchars($_SESSION['name']) ?>
        </span>
        <a href="index.php" class="btn btn-light btn-sm">Data Laporan</a>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</div>
</nav>

<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary">Data Siswa</h1>
        <a href="student_create.php" class="btn btn-primary fw-semibold">
            + Tambah Siswa
        </a>

        
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            if ($_GET['success'] == 'created') echo "Siswa berhasil ditambahkan";
            elseif ($_GET['success'] == 'updated') echo "Data siswa berhasil diupdate";
            elseif ($_GET['success'] == 'deleted') echo "Siswa berhasil dihapus";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NISN</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    Belum ada data siswa.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($student['name']) ?></td>
                                    <td><?= htmlspecialchars($student['nisn']) ?></td>
                                    <td><?= htmlspecialchars($student['nis'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($student['class'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($student['email'] ?? '-') ?></td>
                                    <td style="max-width: 200px;">
                                        <div class="text-truncate">
                                            <?= htmlspecialchars($student['alamat'] ?? '-') ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="student_edit.php?id=<?= $student['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>
                                            <a href="student_delete.php?id=<?= $student['id'] ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Semua laporan terkait juga akan terhapus.')">
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
