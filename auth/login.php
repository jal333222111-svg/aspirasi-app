<?php
require_once '../config/database.php';
require_once '../config/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('../index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($type === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_type'] = 'admin';
            redirect('../admin/index.php');
        } else {
            $error = "Username atau password admin salah";
        }
    } else {
        $stmt = $pdo->prepare("SELECT * FROM student WHERE nisn = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_type'] = 'student';
            redirect('../student/index.php');
        } else {
            $error = "NISN atau password siswa salah";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | ReportApp</title>

<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #0AC4E0, #0B2D72);
    min-height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-card {
    width: 100%;
    max-width: 380px;
    padding: 15px;
}

.card {
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(18px);
    border-radius: 20px;
    border: none;
    color: #fff;
    box-shadow: 0 25px 50px rgba(0,0,0,.25);
}

.logo {
    height: 50px;
}

.btn-group {
    background: rgba(255,255,255,0.2);
    padding: 4px;
    border-radius: 12px;
}

.btn-group .btn {
    border-radius: 10px !important;
    border: none;
    color: #fff;
    font-weight: 500;
    font-size: 14px;
}

.btn-check:checked + .btn {
    background: #1e3a8a;
    color: #fff;
}

.form-label {
    font-size: 13px;
    margin-bottom: 4px;
}

.form-control {
    border-radius: 10px;
    padding: 10px;
    border: none;
    background: rgba(255,255,255,0.95);
}

.form-control:focus {
    box-shadow: 0 0 0 2px rgba(255,255,255,.4);
}

.btn-primary {
    border-radius: 10px;
    padding: 10px;
    font-weight: 600;
    background: #1e3a8a;
    border: none;
}

.btn-primary:hover {
    background: #162d6b;
}

.alert {
    border-radius: 10px;
    font-size: 13px;
}

/* RESPONSIVE */
@media (max-width: 576px) {
    .login-card {
        max-width: 100%;
    }

    .card {
        padding: 15px;
    }

    .logo {
        height: 40px;
    }
}
</style>
</head>

<body>

<div class="login-card">
    <div class="card p-4">
        <div class="text-center mb-3">
            <img src="../assets/img/logo.svg" class="logo" alt="Logo">
        </div>

        <p class="text-center mb-4" style="opacity:.85; font-size:14px;">
            Masuk untuk melanjutkan ke aplikasi
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center mb-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="btn-group w-100 mb-4">
                <input type="radio" class="btn-check" name="type" value="student"
                       id="student" checked onchange="updateType('student')">
                <label class="btn" for="student">Siswa</label>

                <input type="radio" class="btn-check" name="type" value="admin"
                       id="admin" onchange="updateType('admin')">
                <label class="btn" for="admin">Admin</label>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" id="username-label">NISN</label>
                <input type="text" class="form-control" id="username" name="username"
                       required placeholder="Masukkan NISN">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" name="password"
                       required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>

        </form>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
function updateType(type) {
    const label = document.getElementById('username-label');
    const input = document.getElementById('username');

    if (type === 'student') {
        label.innerText = 'NISN';
        input.placeholder = 'Masukkan NISN';
    } else {
        label.innerText = 'Username';
        input.placeholder = 'Masukkan Username';
    }

    input.value = '';
}
</script>

</body>
</html>