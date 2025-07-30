<?php
$pageTitle = "Daftar Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';

// Query dengan JOIN untuk mendapatkan data nilai lengkap
$sql = 'SELECT 
    n.id,
    s.nama AS nama_siswa,
    s.nis AS nis_siswa,
    mp.nama AS nama_mata_pelajaran,
    k.nama AS nama_kelas,
    ta.nama AS nama_tahun_ajaran,
    nj.nama AS jenis_nilai,
    n.nilai,
    n.tanggal_penilaian,
    n.keterangan
FROM nilai n
JOIN siswa s ON n.id_siswa = s.id
JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
JOIN kelas k ON n.id_kelas = k.id
JOIN tahun_ajaran ta ON n.id_tahun_ajaran = ta.id
JOIN nilai_jenis nj ON n.id_jenis_nilai = nj.id';

$params = [];

if ($searchQuery) {
    $sql .= ' WHERE s.nama LIKE :search_siswa 
              OR s.nis LIKE :search_nis
              OR mp.nama LIKE :search_mapel 
              OR k.nama LIKE :search_kelas 
              OR ta.nama LIKE :search_tahun
              OR nj.nama LIKE :search_jenis
              OR n.keterangan LIKE :search_keterangan';
    $params[':search_siswa'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_mapel'] = '%' . $searchQuery . '%';
    $params[':search_kelas'] = '%' . $searchQuery . '%';
    $params[':search_tahun'] = '%' . $searchQuery . '%';
    $params[':search_jenis'] = '%' . $searchQuery . '%';
    $params[':search_keterangan'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY n.tanggal_penilaian DESC, s.nama ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$nilaiList = $stmt->fetchAll();

ob_start();
?>
<h1 class="mb-4">Data Nilai</h1>

<div class="d-flex justify-content-between mb-3">
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari data nilai..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($searchQuery): ?>
                <a href="nilai" class="btn btn-outline-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai/create" class="btn btn-primary me-2">
            <i class="bi bi-plus"></i> Tambah Nilai
        </a>
        <a href="nilai/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="btn btn-success">
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
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Tahun Ajaran</th>
                <th>Jenis Nilai</th>
                <th>Nilai</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($nilaiList) > 0): ?>
                <?php foreach ($nilaiList as $nilai): ?>
                    <tr>
                        <td><?= htmlspecialchars($nilai['id']) ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($nilai['nama_siswa']) ?></div>
                            <small class="text-muted">NIS: <?= htmlspecialchars($nilai['nis_siswa']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($nilai['nama_mata_pelajaran']) ?></td>
                        <td><?= htmlspecialchars($nilai['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($nilai['nama_tahun_ajaran']) ?></td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($nilai['jenis_nilai']) ?></span>
                        </td>
                        <td>
                            <span class="fw-bold fs-5 
                                <?php 
                                $nilaiNum = (float)$nilai['nilai'];
                                if ($nilaiNum >= 80) echo 'text-success';
                                elseif ($nilaiNum >= 70) echo 'text-warning';
                                else echo 'text-danger';
                                ?>">
                                <?= htmlspecialchars($nilai['nilai']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($nilai['tanggal_penilaian'])) ?></td>
                        <td>
                            <?php if ($nilai['keterangan']): ?>
                                <?= htmlspecialchars($nilai['keterangan']) ?>
                            <?php else: ?>
                                <em class="text-muted">-</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="nilai/details?id=<?= htmlspecialchars($nilai['id']) ?>" class="btn btn-sm btn-secondary me-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="nilai/edit?id=<?= htmlspecialchars($nilai['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="nilai/delete?id=<?= htmlspecialchars($nilai['id']) ?>" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data nilai ditemukan.</td>
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