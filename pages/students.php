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
    <h1 class="mb-6 text-2xl font-bold text-primary-700">Data Siswa</h1>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <form action="" method="GET" class="flex flex-1 gap-2">
            <input type="text" name="search"
                   class="flex-1 rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                   placeholder="Cari data siswa..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition"
                    type="submit">
                <iconify-icon icon="cil:search" class="text-lg"></iconify-icon>
                Cari
            </button>
            <?php if ($searchQuery): ?>
                <a href="students"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">Reset</a>
            <?php endif; ?>
        </form>
        <div class="flex gap-2">
            <a href="<?= htmlspecialchars($urlPrefix) ?>/students/create"
               class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                <iconify-icon icon="cil:plus" class="text-lg"></iconify-icon>
                Tambah Data
            </a>
            <a href="students/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>"
               class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
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
                <th class="px-4 py-2 font-semibold">NIS</th>
                <th class="px-4 py-2 font-semibold">NISN</th>
                <th class="px-4 py-2 font-semibold">Nama</th>
                <th class="px-4 py-2 font-semibold">Tanggal Lahir</th>
                <th class="px-4 py-2 font-semibold">Jenis Kelamin</th>
                <th class="px-4 py-2 font-semibold">Alamat</th>
                <th class="px-4 py-2 font-semibold">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $siswa): ?>
                    <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['id']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['nis']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['nisn']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($siswa['nama']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($siswa['tanggal_lahir']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= $siswa['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($siswa['alamat']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap flex gap-1">
                            <a href="students/details?id=<?= htmlspecialchars($siswa['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-info-100 text-status-info-700 hover:bg-status-info-200 transition"
                               title="Detail">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </a>
                            <a href="students/edit?id=<?= htmlspecialchars($siswa['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-warning-100 text-status-warning-700 hover:bg-status-warning-200 transition"
                               title="Edit">
                                <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                            </a>
                            <a href="students/delete?id=<?= htmlspecialchars($siswa['id']) ?>"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition"
                               title="Hapus">
                                <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center py-8 text-secondary-500">Tidak ada data siswa ditemukan.</td>
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