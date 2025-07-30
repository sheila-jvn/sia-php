<?php
$pageTitle = "Daftar Kelas";
$currentPage = 'classes';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';
$sql = 'SELECT 
    k.id, 
    k.nama AS nama_kelas, 
    ta.nama AS nama_tahun_ajaran, 
    g.nama AS nama_guru_wali, 
    t.nama AS nama_tingkat 
FROM kelas k 
JOIN tahun_ajaran ta ON k.id_tahun_ajaran = ta.id 
JOIN tingkat t ON k.id_tingkat = t.id 
LEFT JOIN guru g ON k.id_guru_wali = g.id ';
$params = [];

if ($searchQuery) {
    $sql .= ' WHERE k.nama LIKE :search_nama OR ta.nama LIKE :search_tahun OR t.nama LIKE :search_tingkat OR g.nama LIKE :search_guru';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_tahun'] = '%' . $searchQuery . '%';
    $params[':search_tingkat'] = '%' . $searchQuery . '%';
    $params[':search_guru'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY k.nama'; 

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$classes = $stmt->fetchAll();

ob_start();
?>
<h1 class="mb-4">Data Kelas</h1>

<div class="d-flex justify-content-between mb-3">
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari data kelas..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($searchQuery): ?>
                <a href="classes" class="btn btn-outline-secondary ms-2">Reset</a> 
            <?php endif; ?>
        </form>
    </div>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/classes/create" class="btn btn-primary me-2"> 
            <i class="bi bi-plus"></i> Tambah Data
        </a>
        <a href="classes/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="btn btn-success"> 
            <i class="bi bi-file-earmark-arrow-up"></i> Export Data
        </a>    
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Nama Kelas</th>
                <th>Tahun Ajaran</th>
                <th>Wali Kelas</th>
                <th>Tingkat</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($classes) > 0): ?> 
                <?php foreach ($classes as $kelas): ?> 
                    <tr>
                        <td><?= htmlspecialchars($kelas['id']) ?></td>
                        <td><?= htmlspecialchars($kelas['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($kelas['nama_tahun_ajaran']) ?></td>
                        <td><?= htmlspecialchars($kelas['nama_guru_wali']) ?></td>
                        <td><?= htmlspecialchars($kelas['nama_tingkat']) ?></td>
                        <td>
                            <a href="classes/details?id=<?= htmlspecialchars($kelas['id']) ?>" class="btn btn-secondary me-1" title="Detail"> 
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="classes/edit?id=<?= htmlspecialchars($kelas['id']) ?>" class="btn btn-outline-primary me-1" title="Edit"> 
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="classes/delete?id=<?= htmlspecialchars($kelas['id']) ?>" class="btn btn-danger" title="Hapus"> 
                                <i class="bi bi-trash"></i>
                            </a>                        
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data kelas ditemukan.</td> 
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