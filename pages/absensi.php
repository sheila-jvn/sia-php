<?php
$pageTitle = "Daftar Absensi";
$currentPage = 'absensi';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';

$sql = 'SELECT 
    kh.id,
    s.nama AS nama_siswa,
    s.nis AS nis_siswa,
    k.nama AS nama_kelas,
    ta.nama AS nama_tahun_ajaran,
    ks.nama AS status_kehadiran,
    kh.tanggal,
    kh.keterangan
FROM kehadiran kh
JOIN siswa s ON kh.id_siswa = s.id
JOIN kelas k ON kh.id_kelas = k.id
JOIN tahun_ajaran ta ON kh.id_tahun_ajaran = ta.id
JOIN kehadiran_status ks ON kh.id_status = ks.id';

$params = [];

if ($searchQuery) {
    $sql .= ' WHERE s.nama LIKE :search_siswa 
              OR s.nis LIKE :search_nis
              OR k.nama LIKE :search_kelas 
              OR ta.nama LIKE :search_tahun
              OR ks.nama LIKE :search_status
              OR kh.keterangan LIKE :search_keterangan';
    $params[':search_siswa'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_kelas'] = '%' . $searchQuery . '%';
    $params[':search_tahun'] = '%' . $searchQuery . '%';
    $params[':search_status'] = '%' . $searchQuery . '%';
    $params[':search_keterangan'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY kh.tanggal DESC, s.nama ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$absensiList = $stmt->fetchAll();

ob_start();
?>
<h1 class="mb-4">Data Absensi</h1>

<div class="d-flex justify-content-between mb-3">
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari data absensi..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($searchQuery): ?>
                <a href="absensi" class="btn btn-outline-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi/create" class="btn btn-primary me-2">
            <i class="bi bi-plus"></i> Tambah Absensi
        </a>
        <a href="absensi/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-arrow-up"></i> Export Data
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Tahun Ajaran</th>
                <th>Status Kehadiran</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($absensiList) > 0): ?>
                <?php foreach ($absensiList as $absensi): ?>
                    <tr>
                        <td><?= htmlspecialchars($absensi['id']) ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($absensi['nama_siswa']) ?></div>
                            <small class="text-muted">NIS: <?= htmlspecialchars($absensi['nis_siswa']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($absensi['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($absensi['nama_tahun_ajaran']) ?></td>
                        <td>
                            <?php 
                            $status = strtolower($absensi['status_kehadiran']);
                            $badgeClass = 'bg-secondary';
                            if (strpos($status, 'hadir') !== false) {
                                $badgeClass = 'bg-success';
                            } elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) {
                                $badgeClass = 'bg-danger';
                            } elseif (strpos($status, 'izin') !== false) {
                                $badgeClass = 'bg-warning';
                            } elseif (strpos($status, 'sakit') !== false) {
                                $badgeClass = 'bg-info';
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($absensi['status_kehadiran']) ?></span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></td>
                        <td>
                            <?php if ($absensi['keterangan']): ?>
                                <?= htmlspecialchars($absensi['keterangan']) ?>
                            <?php else: ?>
                                <em class="text-muted">-</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="absensi/details?id=<?= htmlspecialchars($absensi['id']) ?>" class="btn btn-sm btn-secondary me-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="absensi/edit?id=<?= htmlspecialchars($absensi['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="absensi/delete?id=<?= htmlspecialchars($absensi['id']) ?>" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data absensi ditemukan.</td>
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