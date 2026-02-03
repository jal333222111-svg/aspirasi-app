<?php
require_once '../config/database.php';
require_once '../config/functions.php';
if (isset($_SESSION['user_id'])) {
    redirect('../index.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; // 'student' or 'admin'
    $username = $_POST['username'];
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
            $error = "Invalid admin credentials";
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
            $error = "Invalid student credentials (use NISN as username)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Report App</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            /* background-image: url("../assets/bg.jpg");
            min-height: 100vh; */
        }
        .login-card {
            width: 480px;
            margin: 0 auto;
        }
        .card-body {
            position: relative;
        }
        .error-placeholder {
            position: absolute;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 80px);
            z-index: 10;
        }
        form {
            margin-top: 70px;
        }
        .type-btn input[type="radio"] {
            display: none;
        }
        .type-btn {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-card">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h1 class="text-center fw-bold text-primary mb-4">ReportApp</h1>
                    <div class="error-placeholder">
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center mb-0" role="alert">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <form method="POST">
                        <div class="btn-group w-100 mb-4" role="group">
                            <input type="radio" class="btn-check" name="type" value="student" id="btn-student" checked onchange="updateType('student')">
                            <label class="btn btn-outline-primary" for="btn-student">Siswa</label>
                            <input type="radio" class="btn-check" name="type" value="admin" id="btn-admin" onchange="updateType('admin')">
                            <label class="btn btn-outline-primary" for="btn-admin">Admin</label>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold" id="username-label">NISN</label>
                            <input type="text" class="form-control" id="username" name="username" required placeholder="Enter your identifier">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In</button>
                        <div class="text-center mt-3">
                            <a href="register.php" class="text-decoration-none">Belum punya akun admin? Daftar disini</a>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateType(type) {
            document.getElementById('username-label').innerText = type === 'student' ? 'NISN' : 'Username';
        }
    </script>
</body>
</html>