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
<h1 class="mb-6 text-2xl font-bold text-primary-700">Data Kelas</h1>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
    <form action="" method="GET" class="flex flex-1 gap-2">
        <input type="text" name="search" class="flex-1 rounded-lg border border-secondary-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" placeholder="Cari data kelas..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition" type="submit">
            <iconify-icon icon="cil:search" width="20" height="20"></iconify-icon>
            Cari
        </button>
        <?php if ($searchQuery): ?>
            <a href="classes" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition ml-2">Reset</a>
        <?php endif; ?>
    </form>
    <div class="flex gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/classes/create" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
            <iconify-icon icon="cil:plus" width="20" height="20"></iconify-icon>
            Tambah Data
        </a>
        <a href="classes/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
            <iconify-icon icon="cil:file-export" width="20" height="20"></iconify-icon>
            Export Data
        </a>
    </div>
</div>

<div class="overflow-x-auto rounded-lg border border-secondary-200 bg-white">
    <table class="min-w-full divide-y divide-secondary-200">
        <thead class="bg-primary-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700">ID</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700">Nama Kelas</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700">Tahun Ajaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700">Wali Kelas</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700">Tingkat</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-primary-700">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-100">
            <?php if (count($classes) > 0): ?>
                <?php foreach ($classes as $kelas): ?>
                    <tr class="hover:bg-secondary-50">
                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($kelas['id']) ?></td>
                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($kelas['nama_kelas']) ?></td>
                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($kelas['nama_tahun_ajaran']) ?></td>
                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($kelas['nama_guru_wali']) ?></td>
                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($kelas['nama_tingkat']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap flex justify-center gap-1">
    <a href="classes/details?id=<?= htmlspecialchars($kelas['id']) ?>"
       class="inline-flex items-center justify-center p-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition"
       title="Detail">
        <iconify-icon icon="mdi:eye-outline" width="20" height="20"></iconify-icon>
    </a>
    <a href="classes/edit?id=<?= htmlspecialchars($kelas['id']) ?>"
       class="inline-flex items-center justify-center p-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition"
       title="Edit">
        <iconify-icon icon="mdi:pencil-outline" width="20" height="20"></iconify-icon>
    </a>
    <a href="classes/delete?id=<?= htmlspecialchars($kelas['id']) ?>"
       class="inline-flex items-center justify-center p-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition"
       title="Hapus">
        <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
    </a>
</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-8 text-secondary-500">Tidak ada data kelas ditemukan.</td>
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