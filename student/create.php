<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('student');

$stmt = $pdo->query("SELECT * FROM category");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $report_text = $_POST['report'];
    $category_id = $_POST['category_id'];
    $student_id = $_SESSION['user_id'];
    $date = date('Y-m-d');
    $status = 'menunggu';

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO report (report, report_date, student_id, status, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$report_text, $date, $student_id, $status, $category_id]);

        $report_id = $pdo->lastInsertId();

        // Upload multiple images
        if (!empty($_FILES['images']['name'][0])) {

            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {

                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {

                    $file_ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $file_name = time() . '_' . uniqid() . '.' . $file_ext;
                    $target_file = $upload_dir . $file_name;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $stmt = $pdo->prepare("INSERT INTO report_picture (report_id, picture) VALUES (?, ?)");
                        $stmt->execute([$report_id, $file_name]);
                    }
                }
            }
        }

        $pdo->commit();
        redirect('index.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to submit report: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Report - Student</title>
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --main-color: #0992C2;
        --main-hover: #0779a1;
    }

    body {
        background-color: #f4f6f9;
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

    .form-control:focus,
    .form-select:focus {
        border-color: var(--main-color);
        box-shadow: 0 0 0 0.2rem rgba(9,146,194,0.25);
    }

    .preview-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
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
        <a href="index.php" class="btn btn-light btn-sm fw-semibold">Kembali</a>
    </div>
</div>
</nav>

<div class="container my-5" style="max-width:800px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-4" style="color:#0992C2;">Buat Laporan Baru</h4>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="report-form">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Isi Laporan</label>
                    <textarea name="report" rows="5" class="form-control" required placeholder="Ceritakan detail laporan..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Gambar</label>
                    <input type="file" name="images[]" multiple accept="image/*" id="image-input" class="form-control">
                    <div class="d-flex gap-3 flex-wrap mt-3" id="preview-grid"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-3">
                    Kirim Laporan
                </button>

            </form>
        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
const imageInput = document.getElementById('image-input');
const previewGrid = document.getElementById('preview-grid');

imageInput.addEventListener('change', function(e) {

    previewGrid.innerHTML = '';

    Array.from(e.target.files).forEach(file => {

        const reader = new FileReader();

        reader.onload = function(event) {

            const container = document.createElement('div');
            container.style.position = 'relative';

            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'preview-img border shadow-sm';

            container.appendChild(img);
            previewGrid.appendChild(container);
        };

        reader.readAsDataURL(file);
    });
});
</script>

</body>
</html>
