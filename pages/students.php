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
<h1 class="mb-4">Data Siswa</h1>

<div class="d-flex justify-content-between mb-3">
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari data siswa..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($searchQuery): ?>
                <a href="students" class="btn btn-outline-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div>
<a href="<?= htmlspecialchars($urlPrefix) ?>/students/create" class="btn btn-primary me-2"> <i class="bi bi-plus"></i> Tambah Data</a>
         <a href="students/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="btn btn-success">
             <i class="bi bi-file-earmark-arrow-up"></i> Export Data</a>    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-primary">
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
<a href="students/details?id=<?= htmlspecialchars($siswa['id']) ?>" class="btn btn-secondary me-1" title="Detail">
                                 <i class="bi bi-eye"></i>
                             </a>
                             <a href="students/edit?id=<?= htmlspecialchars($siswa['id']) ?>" class="btn btn-outline-primary me-1" title="Edit">
                                 <i class="bi bi-pencil"></i>
                             </a>
                             <a href="students/delete?id=<?= htmlspecialchars($siswa['id']) ?>" class="btn btn-danger" title="Hapus">
                                 <i class="bi bi-trash"></i>
                             </a>                        </td>
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