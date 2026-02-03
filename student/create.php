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
        // Handle multiple file uploads
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = time() . '_' . $_FILES['images']['name'][$key];
                    $target_file = $upload_dir . basename($file_name);
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
        .preview-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-primary">ReportApp</span>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </nav>
    <div class="container my-5" style="max-width: 800px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="fw-bold mb-4">Buat Laporan Baru</h1>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Laporan</label>
                        <textarea name="report" rows="6" class="form-control" required placeholder="Ceritakan detail kejadian atau laporan Anda..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Gambar (Klik lagi untuk menambah gambar)</label>
                        <input type="file" multiple accept="image/*" id="image-input" class="form-control">
                        <div class="d-flex gap-3 flex-wrap mt-3" id="preview-grid"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-3">Kirim Laporan</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFiles = [];
        const imageInput = document.getElementById('image-input');
        const previewGrid = document.getElementById('preview-grid');
        const form = document.querySelector('form');
        imageInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    selectedFiles.push(file);
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const container = document.createElement('div');
                        container.style.position = 'relative';
                        container.className = 'preview-wrapper';
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.className = 'preview-img border shadow-sm';
                        const removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '×';
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                        removeBtn.style.cssText = 'transform: translate(50%, -50%); width: 24px; height: 24px; padding: 0; border-radius: 50%;';
                        removeBtn.onclick = function() {
                            selectedFiles = selectedFiles.filter(f => f !== file);
                            container.remove();
                        };
                        container.appendChild(img);
                        container.appendChild(removeBtn);
                        previewGrid.appendChild(container);
                    }
                    reader.readAsDataURL(file);
                }
            });
            imageInput.value = '';
        });
        form.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            selectedFiles.forEach(file => {
                formData.append('images[]', file);
            });
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Mengirim...';
            fetch('create.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'index.php';
                } else {
                    alert('Gagal mengirim laporan.');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Kirim Laporan';
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem.');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Kirim Laporan';
            });
        };
    </script>
</body>
</html>
