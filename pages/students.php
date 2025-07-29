<?php
$pageTitle = "Daftar Siswa";
$currentPage = 'students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';
$sql = 'SELECT * FROM siswa';
$params = [];

if ($searchQuery) {
    $sql .= ' WHERE nama LIKE :search_nama OR nis LIKE :search_nis OR nisn LIKE :search_nisn OR alamat LIKE :search_alamat';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_nisn'] = '%' . $searchQuery . '%';
    $params[':search_alamat'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY nama';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

ob_start();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    :root {
        --primary-blue: #2c5282;
        --light-blue: #ebf8ff;
        --text-color: #4a5568;
        --border-color: #cbd5e0;
        --bg-color: #f7fafc;
    }
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }

    .card {
        border-color: var(--border-color);
    }

    .btn-primary {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #2a4365;
        border-color: #2a4365;
    }

    .btn-info {
        background-color: #3182ce;
        border-color: #3182ce;
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-info:hover {
        background-color: #2c5282;
        border-color: #2c5282;
    }

    .btn-warning {
        background-color: #f6ad55;
        border-color: #f6ad55;
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-warning:hover {
        background-color: #ed8936;
        border-color: #ed8936;
    }

    .btn-danger {
        background-color: #e53e3e;
        border-color: #e53e3e;
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-danger:hover {
        background-color: #c53030;
        border-color: #c53030;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.25);
    }

    .text-decoration-none {
        color: var(--primary-blue) !important;
    }

    .text-decoration-none:hover {
        text-decoration: underline !important;
    }
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .table-bordered th,
    .table-bordered td {
        border-color: var(--border-color);
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: var(--light-blue);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(44, 82, 130, 0.1);
    }

    .table-custom-dark-blue {
        background-color: var(--primary-blue);
        color: white;
    }
</style>

<h1 class="mb-4">Data Siswa</h1>

<div class="d-flex justify-content-between mb-3">
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari data siswa..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($searchQuery): ?>
                <a href="students" class="btn btn-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/students-create" class="btn btn-primary me-2"> <i class="fas fa-plus"></i> Tambah Data
        </a>
        <a href="export_students.php<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="btn btn-info">
            <i class="fas fa-file-export"></i> Export Data
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-custom-dark-blue">
            <tr>
                <th>ID</th>
                <th>NIS</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $siswa): ?>
                    <tr>
                        <td><?= htmlspecialchars($siswa['id']) ?></td>
                        <td><?= htmlspecialchars($siswa['nis']) ?></td>
                        <td><?= htmlspecialchars($siswa['nisn']) ?></td>
                        <td><?= htmlspecialchars($siswa['nama']) ?></td>
                        <td><?= htmlspecialchars($siswa['tanggal_lahir']) ?></td>
                        <td><?= $siswa['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td><?= htmlspecialchars($siswa['alamat']) ?></td>
                        <td>
                            <a href="students-detail?id=<?= htmlspecialchars($siswa['id']) ?>" class="btn btn-info me-1" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_student.php?id=<?= htmlspecialchars($siswa['id']) ?>" class="btn btn-warning me-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_student.php?id=<?= htmlspecialchars($siswa['id']) ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data siswa ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>