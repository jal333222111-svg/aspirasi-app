<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = strtolower(trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = "Username sudah digunakan";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO admin (name, username, password) VALUES (?, ?, ?)");

            if ($stmt->execute([$name, $username, $hashed_password])) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Gagal melakukan registrasi";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #066b8f, #0992C2);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .register-card {
            width: 100%;
            max-width: 420px;
        }

        .card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(16px);
            border-radius: 25px;
            border: none;
            color: #fff;
            box-shadow: 0 25px 50px rgba(0,0,0,.3);
        }

        .form-control {
            border-radius: 15px;
            padding: 14px;
            border: none;
            background: rgba(255,255,255,0.9);
        }

        .form-control::placeholder {
            color: #888;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px rgba(9,146,194,.5);
        }

        .btn-primary {
            border-radius: 15px;
            padding: 14px;
            font-weight: 600;
            background: #163e73;
            border: none;
        }

        .btn-primary:hover {
            background: #0f2f59;
        }

        .alert {
            border-radius: 12px;
            font-size: 14px;
        }

        .form-text {
            color: #e6f9ff;
        }

        a {
            color: #ff4d4d;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        h1 {
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="register-card">
        <div class="card p-4">
            <div class="card-body">

                <!-- HEADER -->
                <h1 class="text-center fw-bold mb-2">Asschool</h1>
                <p class="text-center mb-4" style="opacity:.85;">
                    Registrasi untuk mendapatkan akun
                </p>

                <!-- ALERT -->
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <!-- FORM -->
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" required
                               placeholder="isi Nama Lengkap"
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" name="username" required
                               placeholder="isi username"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" name="password"
                               required minlength="6" placeholder="******">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi password</label>
                        <input type="password" class="form-control" name="confirm_password"
                               required minlength="6" placeholder="******">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Login
                    </button>

                    <div class="text-center">
                        Sudah Punya akun admin?
                        <a href="login.php">Klik disini</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>