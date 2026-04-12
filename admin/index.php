<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkLogin('admin');

$search = $_GET['search'] ?? '';
$type   = $_GET['type'] ?? 'all';
$params = [];

$sql = "
    SELECT r.*, s.name AS student_name, c.name AS category_name
    FROM report r
    JOIN student s ON r.student_id = s.id
    JOIN category c ON r.category_id = c.id
";

if (!empty($search)) {

    if ($type === 'name') {
        $sql .= " WHERE s.name LIKE ?";
        $params[] = "%$search%";

    } elseif ($type === 'category') {
        $sql .= " WHERE c.name = ?";
        $params[] = $search;

    } elseif ($type === 'date') {

        if (is_numeric($search)) {
            $num = (int)$search;

            if ($num >= 1 && $num <= 31) {
                $sql .= " WHERE DAY(r.report_date) = ?";
                $params[] = $num;
            }
        }

    } else {
        $sql .= " WHERE 
            s.name LIKE ? OR 
            c.name LIKE ? OR 
            r.report LIKE ?
        ";
        $params = ["%$search%", "%$search%", "%$search%"];
    }
}

$sql .= " ORDER BY r.report_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f2f4f7; font-family:'Segoe UI'; }

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

.page-title { color:#0992C2; font-weight:600; }

.card { border-radius:12px; }

.search-group { display:flex; width:300px; }

.search-input {
    border:none;
    padding:8px;
    flex:1;
    font-size:14px;
    border-radius:10px 0 0 10px;
}

.search-input:focus { outline:none; }

.dropdown-toggle { border-radius:0 10px 10px 0; }

.badge-status {
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

.menunggu { background:#ffe8a1; }
.proses { background:#cfe9ff; }
.selesai { background:#c8f7dc; }

@media(max-width:768px){
    .search-group{ width:100%; }
}
</style>
</head>

<body>
    
<!-- NAVBAR -->
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
        <a href="students.php" class="btn btn-light btn-sm">Data siswa</a>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</div>
</nav>


<div class="container my-5">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="page-title">Semua Laporan</h5>

    <div class="btn-group search-group">
        <div id="searchContainer" style="flex:1;"></div>

        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"></button>

        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" onclick="setType('all')">Semua</a></li>
            <li><a class="dropdown-item" onclick="setType('name')">Nama</a></li>
            <li><a class="dropdown-item" onclick="setType('category')">Kategori</a></li>
            <li><a class="dropdown-item" onclick="setType('date')">Tanggal</a></li>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
<div class="card-body p-0">

<div class="table-responsive">
<table class="table mb-0 align-middle">

<thead>
<tr>
<th>ID</th>
<th>Siswa</th>
<th>Kategori</th>
<th>Laporan</th>
<th>Tanggal</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php foreach ($reports as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['student_name']) ?></td>
<td><?= htmlspecialchars($r['category_name']) ?></td>

<td class="text-truncate" style="max-width:200px;">
<?= htmlspecialchars($r['report']) ?>
</td>

<td><?= formatDate($r['report_date']) ?></td>

<td>
<span class="badge-status <?= $r['status'] ?>">
<?= ucfirst($r['status']) ?>
</span>
</td>

<td>
<a href="feedback.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">Tanggapi</a>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-danger btn-sm">Hapus</a>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let type = "<?= $type ?>";
let currentValue = "<?= htmlspecialchars($search) ?>";
let timeout = null;

function renderInput(){
    const container = document.getElementById('searchContainer');

    if(type === 'category'){
        container.innerHTML = `
            <select id="searchInput" class="form-select">
                <option value="">Pilih kategori</option>
                <option ${currentValue=='Sarana prasarana'?'selected':''}>Sarana prasarana</option>
                <option ${currentValue=='Pelayanan'?'selected':''}>Pelayanan</option>
                <option ${currentValue=='Keamanan'?'selected':''}>Keamanan</option>
                <option ${currentValue=='Lainnya'?'selected':''}>Lainnya</option>
            </select>
        `;

        document.getElementById('searchInput')
            .addEventListener('change', doSearch);

    } else if(type === 'date'){
        container.innerHTML = `
            <input type="number" id="searchInput" class="search-input"
            min="1" max="31"
            placeholder="Tanggal (1-31)"
            value="${currentValue}">
        `;

        const input = document.getElementById('searchInput');

        input.addEventListener('input', ()=>{
            if(input.value > 31) input.value = 31;
            if(input.value < 1) input.value = 1;
        });

        input.addEventListener('keyup', ()=>{
            clearTimeout(timeout);
            timeout = setTimeout(doSearch,400);
        });

    } else {
        container.innerHTML = `
            <input type="text" id="searchInput" class="search-input"
            placeholder="Search..."
            value="${currentValue}">
        `;

        const input = document.getElementById('searchInput');

        input.addEventListener('keyup', ()=>{
            clearTimeout(timeout);
            timeout = setTimeout(doSearch,400);
        });
    }
}

function setType(t){
    type = t;
    currentValue = '';
    renderInput();
}

function doSearch(){
    const input = document.getElementById('searchInput');
    let value = input.value;

    window.location.href = `?search=${value}&type=${type}`;
}

renderInput();
</script>

</body>
</html>