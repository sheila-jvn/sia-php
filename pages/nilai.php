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
<h1 class="mb-6 text-2xl font-bold text-primary-700">Data Nilai</h1>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <form action="" method="GET" class="flex flex-1 gap-2">
        <input type="text" name="search" class="flex-1 rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm" placeholder="Cari data nilai..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition" type="submit">
            <iconify-icon icon="cil:search" class="text-lg"></iconify-icon>
            Cari
        </button>
        <?php if ($searchQuery): ?>
            <a href="nilai" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">Reset</a>
        <?php endif; ?>
    </form>
    <div class="flex gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai/create" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
            <iconify-icon icon="cil:plus" class="text-lg"></iconify-icon>
            Tambah Nilai
        </a>
        <a href="nilai/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
            <iconify-icon icon="cil:file-export" class="text-lg"></iconify-icon>
            Export Data
        </a>
    </div>
</div>

<div class="overflow-x-auto rounded-lg shadow border border-secondary-200 bg-white">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-primary-100 text-primary-700">
            <tr>
                <th class="px-4 py-2 font-semibold">ID</th>
                <th class="px-4 py-2 font-semibold">Siswa</th>
                <th class="px-4 py-2 font-semibold">Mata Pelajaran</th>
                <th class="px-4 py-2 font-semibold">Kelas</th>
                <th class="px-4 py-2 font-semibold">Tahun Ajaran</th>
                <th class="px-4 py-2 font-semibold">Jenis Nilai</th>
                <th class="px-4 py-2 font-semibold">Nilai</th>
                <th class="px-4 py-2 font-semibold">Tanggal</th>
                <th class="px-4 py-2 font-semibold">Keterangan</th>
                <th class="px-4 py-2 font-semibold">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($nilaiList) > 0): ?>
                <?php foreach ($nilaiList as $nilai): ?>
                    <?php
                    $nilaiNum = (float)$nilai['nilai'];
                    $nilaiColor = 'text-status-error-600';
                    if ($nilaiNum >= 80) {
                        $nilaiColor = 'text-status-success-700';
                    } elseif ($nilaiNum >= 70) {
                        $nilaiColor = 'text-status-warning-700';
                    }
                    ?>
                    <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($nilai['id']) ?></td>
                        <td class="px-4 py-2">
                            <div class="font-semibold text-primary-700"><?= htmlspecialchars($nilai['nama_siswa']) ?></div>
                            <div class="text-xs text-gray-500">NIS: <?= htmlspecialchars($nilai['nis_siswa']) ?></div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($nilai['nama_mata_pelajaran']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($nilai['nama_kelas']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($nilai['nama_tahun_ajaran']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold bg-status-info-100 text-status-info-700">
                                <?= htmlspecialchars($nilai['jenis_nilai']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="font-bold text-lg <?= $nilaiColor ?>">
                                <?= htmlspecialchars($nilai['nilai']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= date('d/m/Y', strtotime($nilai['tanggal_penilaian'])) ?></td>
                        <td class="px-4 py-2">
                            <?php if ($nilai['keterangan']): ?>
                                <?= htmlspecialchars($nilai['keterangan']) ?>
                            <?php else: ?>
                                <em class="text-gray-400">-</em>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap flex gap-1 justify-center">
                             <a href="nilai/details?id=<?= htmlspecialchars($nilai['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition"
                               title="Detail">
                                <iconify-icon icon="mdi:eye-outline" width="20" height="20"></iconify-icon>
                            </a>
                            <a href="nilai/edit?id=<?= htmlspecialchars($nilai['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition"
                               title="Edit">
                                <iconify-icon icon="mdi:pencil-outline" width="20" height="20"></iconify-icon>
                            </a>
                            <a href="nilai/delete?id=<?= htmlspecialchars($nilai['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition"
                               title="Hapus">
                                <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
                            </a>                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center py-8 text-secondary-500">Tidak ada data nilai ditemukan.</td>
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