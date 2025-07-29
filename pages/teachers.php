<?php
$pageTitle = "Data Guru";
$currentPage = 'teachers';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';
$sql = 'SELECT * FROM guru';
$params = [];

if ($searchQuery) {
    $sql .= ' WHERE nama LIKE :search_nama OR nip LIKE :search_nip OR no_telpon LIKE :search_no_telpon';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_nip'] = '%' . $searchQuery . '%';
    $params[':search_no_telpon'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY nama';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$teachers = $stmt->fetchAll();

ob_start();
?>

<h1 class="mb-4">Data Guru</h1>

<div class="d-flex justify-content-between mb-3">
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari data guru..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($searchQuery): ?>
                <a href="teachers" class="btn btn-outline-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers/create" class="btn btn-primary me-2"> <i class="bi bi-plus"></i> Tambah Data</a>
        <a href="teachers/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="btn btn-success">
             <i class="bi bi-file-earmark-arrow-up"></i> Export Data</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-custom-dark-blue">
            <tr>
                <th>ID</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>No. Telepon</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($teachers) > 0): ?>
                <?php foreach ($teachers as $guru): ?>
                    <tr>
                        <td><?= htmlspecialchars($guru['id']) ?></td>
                        <td><?= htmlspecialchars($guru['nip']) ?></td>
                        <td><?= htmlspecialchars($guru['nama']) ?></td>
                        <td><?= htmlspecialchars($guru['tanggal_lahir']) ?></td>
                        <td><?= $guru['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td><?= htmlspecialchars($guru['no_telpon']) ?></td>
                        <td>
                            <a href="teachers/details?id=<?= htmlspecialchars($guru['id']) ?>" class="btn btn-secondary me-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="teachers/edit?id=<?= htmlspecialchars($guru['id']) ?>" class="btn btn-outline-primary me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="teachers/delete?id=<?= htmlspecialchars($guru['id']) ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data guru ditemukan.</td>
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