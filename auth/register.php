<?php
require_once '../config/database.php';
require_once '../config/functions.php';
if (isset($_SESSION['user_id'])) {
    redirect('../index.php');
}
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok";
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username sudah digunakan";
        } else {
            // Insert new admin
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin (name, username, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $username, $hashed_password])) {
                $success = "Registrasi berhasil! Silahkan login.";
            } else {
                $error = "Gagal melakukan registrasi";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - Report App</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .register-card {
            width: 480px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="register-card">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h1 class="text-center fw-bold text-primary mb-2">ReportApp</h1>
                    <p class="text-center text-muted mb-4">Registrasi Admin</p>
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $success ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required
                                placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                placeholder="••••••••" minlength="6">
                            <div class="form-text">Minimal 6 karakter</div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                placeholder="••••••••" minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">Daftar</button>
                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none">Sudah punya akun? Login disini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
