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
<h1 class="mb-6 text-2xl font-bold text-primary-700">Data Absensi</h1>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <form action="" method="GET" class="flex flex-1 gap-2">
        <input type="text" name="search" class="flex-1 rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm" placeholder="Cari data absensi..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition" type="submit">
            <iconify-icon icon="cil:search" class="text-lg"></iconify-icon>
            Cari
        </button>
        <?php if ($searchQuery): ?>
            <a href="absensi" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">Reset</a>
        <?php endif; ?>
    </form>
    <div class="flex gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi/create" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
            <iconify-icon icon="cil:plus" class="text-lg"></iconify-icon>
            Tambah Absensi
        </a>
        <a href="absensi/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
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
                <th class="px-4 py-2 font-semibold">Kelas</th>
                <th class="px-4 py-2 font-semibold">Tahun Ajaran</th>
                <th class="px-4 py-2 font-semibold">Status Kehadiran</th>
                <th class="px-4 py-2 font-semibold">Tanggal</th>
                <th class="px-4 py-2 font-semibold">Keterangan</th>
                <th class="px-4 py-2 font-semibold">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($absensiList) > 0): ?>
                <?php foreach ($absensiList as $absensi): ?>
                    <?php
                    $status = strtolower($absensi['status_kehadiran']);
                    $badgeClass = 'bg-secondary-100 text-secondary-700';
                    if (strpos($status, 'hadir') !== false) {
                        $badgeClass = 'bg-status-success-100 text-status-success-700';
                    } elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) {
                        $badgeClass = 'bg-status-error-100 text-status-error-700';
                    } elseif (strpos($status, 'izin') !== false) {
                        $badgeClass = 'bg-status-warning-100 text-status-warning-700';
                    } elseif (strpos($status, 'sakit') !== false) {
                        $badgeClass = 'bg-status-info-100 text-status-info-700';
                    }
                    ?>
                    <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($absensi['id']) ?></td>
                        <td class="px-4 py-2">
                            <div class="font-semibold text-primary-700"><?= htmlspecialchars($absensi['nama_siswa']) ?></div>
                            <div class="text-xs text-gray-500">NIS: <?= htmlspecialchars($absensi['nis_siswa']) ?></div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($absensi['nama_kelas']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($absensi['nama_tahun_ajaran']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
                                <?= htmlspecialchars($absensi['status_kehadiran']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></td>
                        <td class="px-4 py-2">
                            <?php if ($absensi['keterangan']): ?>
                                <?= htmlspecialchars($absensi['keterangan']) ?>
                            <?php else: ?>
                                <em class="text-gray-400">-</em>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap flex gap-1 justify-center">
                            <a href="absensi/details?id=<?= htmlspecialchars($absensi['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-info-100 text-status-info-700 hover:bg-status-info-200 transition"
                               title="Detail">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </a>
                            <a href="absensi/edit?id=<?= htmlspecialchars($absensi['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-warning-100 text-status-warning-700 hover:bg-status-warning-200 transition"
                               title="Edit">
                                <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                            </a>
                            <a href="absensi/delete?id=<?= htmlspecialchars($absensi['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition"
                               title="Hapus">
                                <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center py-8 text-secondary-500">Tidak ada data absensi ditemukan.</td>
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