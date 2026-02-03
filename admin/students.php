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
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-primary">ReportApp Admin</span>
            <div class="d-flex align-items-center gap-3">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">Laporan</a>
                <a href="students.php" class="btn btn-primary btn-sm">Data Siswa</a>
                <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="../auth/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">Data Siswa</h1>
            <a href="student_create.php" class="btn btn-primary">
                + Tambah Siswa
            </a>
        </div>
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
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">Belum ada data siswa.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($students as $student): ?>
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
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="student_edit.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-primary">
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
