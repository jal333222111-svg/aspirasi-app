<?php
require_once '../config/database.php';
require_once '../config/functions.php';
checkLogin('admin');

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

    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM student WHERE nisn = ?");
        $stmt->execute([$nisn]);

        if ($stmt->fetch()) {
            $error = "NISN sudah terdaftar";
        } else {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO student (name, nisn, nis, class, email, alamat, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $nisn, $nis, $class, $email, $alamat, $hashed_password]);
                redirect('students.php?success=created');
            } catch (Exception $e) {
                $error = "Gagal menambahkan siswa: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Siswa - Admin</title>
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<style>
:root{
    --primary: #0992C2;
    --primary-light: #0bb3e0;
    --primary-dark: #066b8f;
}

body{
    background: #f4fbff;
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

.card{
    border: none;
    border-radius: 18px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

h1{
    color: var(--primary-dark);
}

.form-control{
    border-radius: 12px;
    padding: 10px;
}

.form-control:focus{
    box-shadow: 0 0 0 2px rgba(9,146,194,.25);
    border-color: var(--primary);
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

.btn-outline-secondary{
    border-radius: 12px;
}

.alert{
    border-radius: 12px;
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

<div class="container my-5" style="max-width: 850px;">
    <div class="card">
        <div class="card-body p-4">

            <h1 class="fw-bold mb-4">Tambah Siswa Baru</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NISN *</label>
                        <input type="text" name="nisn" class="form-control" required
                               value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>"
                               placeholder="Nomor Induk Siswa Nasional">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIS</label>
                        <input type="text" name="nis" class="form-control"
                               value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>"
                               placeholder="Nomor Induk Siswa">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kelas</label>
                        <input type="text" name="class" class="form-control"
                               value="<?= htmlspecialchars($_POST['class'] ?? '') ?>"
                               placeholder="Contoh: 12 IPA 1">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="email@example.com">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"
                                  placeholder="Alamat lengkap siswa"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6"
                               placeholder="••••••••">
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password *</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6"
                               placeholder="••••••••">
                    </div>

                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill py-2 fw-semibold">
                        Simpan Siswa
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
