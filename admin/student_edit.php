<?php
require_once '../config/database.php';
require_once '../config/functions.php';
checkLogin('admin');

$id = $_GET['id'] ?? null;
if (!$id) redirect('students.php');

// Get student data
$stmt = $pdo->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) redirect('students.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'] ?: null;
    $class = $_POST['class'] ?: null;
    $email = $_POST['email'] ?: null;
    $alamat = $_POST['alamat'] ?: null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if NISN already exists for other students
    $stmt = $pdo->prepare("SELECT id FROM student WHERE nisn = ? AND id != ?");
    $stmt->execute([$nisn, $id]);

    if ($stmt->fetch()) {
        $error = "NISN sudah digunakan oleh siswa lain";
    } else {
        try {
            if (!empty($password)) {
                if ($password !== $confirm_password) {
                    $error = "Password dan konfirmasi password tidak cocok";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE student SET name=?, nisn=?, nis=?, class=?, email=?, alamat=?, password=? WHERE id=?");
                    $stmt->execute([$name, $nisn, $nis, $class, $email, $alamat, $hashed_password, $id]);
                    redirect('students.php?success=updated');
                }
            } else {
                $stmt = $pdo->prepare("UPDATE student SET name=?, nisn=?, nis=?, class=?, email=?, alamat=? WHERE id=?");
                $stmt->execute([$name, $nisn, $nis, $class, $email, $alamat, $id]);
                redirect('students.php?success=updated');
            }
        } catch (Exception $e) {
            $error = "Gagal mengupdate siswa: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa - Admin</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- CUSTOM THEME COLOR -->
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

        /* BUTTON */
        .btn-primary {
            background-color: #0d3b66;
            border: none;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background-color: #0b2f52;
        }


        .text-primary {
            color: #0992C2 !important;
        }

        .card {
            border-radius: 12px;
        }

        .form-control:focus {
            border-color: #0992C2;
            box-shadow: 0 0 0 0.2rem rgba(9, 146, 194, 0.25);
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
        <a href="students.php" class="btn btn-light btn-sm fw-semibold">Kembali</a>
    </div>
</div>
</nav>

<div class="container my-5" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="fw-bold mb-4 text-primary">Edit Data Siswa</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                            value="<?= htmlspecialchars($_POST['name'] ?? $student['name']) ?>"
                            placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NISN <span class="text-danger">*</span></label>
                        <input type="text" name="nisn" class="form-control" required
                            value="<?= htmlspecialchars($_POST['nisn'] ?? $student['nisn']) ?>"
                            placeholder="Nomor Induk Siswa Nasional">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIS</label>
                        <input type="text" name="nis" class="form-control"
                            value="<?= htmlspecialchars($_POST['nis'] ?? $student['nis']) ?>"
                            placeholder="Nomor Induk Siswa">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kelas</label>
                        <input type="text" name="class" class="form-control"
                            value="<?= htmlspecialchars($_POST['class'] ?? $student['class']) ?>"
                            placeholder="Contoh: 12 IPA 1">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($_POST['email'] ?? $student['email']) ?>"
                            placeholder="email@example.com">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"
                            placeholder="Alamat lengkap siswa"><?= htmlspecialchars($_POST['alamat'] ?? $student['alamat']) ?></textarea>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <p class="text-muted small mb-2">Kosongkan password jika tidak ingin mengubah</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password" class="form-control"
                            minlength="6" placeholder="••••••••">
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control"
                            minlength="6" placeholder="••••••••">
                    </div>

                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill py-2 fw-semibold">
                        Update Siswa
                    </button>
                    <a href="students.php" class="btn btn-outline-secondary flex-fill py-2 fw-semibold">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
