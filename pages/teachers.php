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

<h1 class="text-2xl font-bold mb-6 text-primary-700">Data Guru</h1>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <form action="" method="GET" class="flex flex-row gap-2 w-full md:w-1/2">
        <input type="text" name="search"
               class="flex-1 px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
               placeholder="Cari data guru..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition"
                type="submit">
            <iconify-icon icon="mdi:magnify" width="20" height="20"></iconify-icon>
            Cari
        </button>
        <?php if ($searchQuery): ?>
            <a href="teachers"
               class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
                Reset
            </a>
        <?php endif; ?>
    </form>
    <div class="flex flex-row gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers/create"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
            <iconify-icon icon="mdi:plus" width="20" height="20"></iconify-icon>
            Tambah Data
        </a>
        <a href="teachers/export<?= $searchQuery ? ('?search=' . urlencode($searchQuery)) : '' ?>"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
            <iconify-icon icon="mdi:file-arrow-up-outline" width="20" height="20"></iconify-icon>
            Export Data
        </a>
    </div>
</div>

<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm text-left border border-secondary-200">
        <thead class="bg-primary-100 text-primary-700">
        <tr>
            <th class="px-4 py-3 border-b border-secondary-200">ID</th>
            <th class="px-4 py-3 border-b border-secondary-200">NIP</th>
            <th class="px-4 py-3 border-b border-secondary-200">Nama</th>
            <th class="px-4 py-3 border-b border-secondary-200">Tanggal Lahir</th>
            <th class="px-4 py-3 border-b border-secondary-200">Jenis Kelamin</th>
            <th class="px-4 py-3 border-b border-secondary-200">No. Telepon</th>
            <th class="px-4 py-3 border-b border-secondary-200">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($teachers) > 0): ?>
            <?php foreach ($teachers as $guru): ?>
                <tr class="even:bg-secondary-50 hover:bg-secondary-100 transition">
                    <td class="px-4 py-2 border-b border-secondary-100"><?= htmlspecialchars($guru['id']) ?></td>
                    <td class="px-4 py-2 border-b border-secondary-100"><?= htmlspecialchars($guru['nip']) ?></td>
                    <td class="px-4 py-2 border-b border-secondary-100"><?= htmlspecialchars($guru['nama']) ?></td>
                    <td class="px-4 py-2 border-b border-secondary-100"><?= htmlspecialchars($guru['tanggal_lahir']) ?></td>
                    <td class="px-4 py-2 border-b border-secondary-100"><?= $guru['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
                    <td class="px-4 py-2 border-b border-secondary-100"><?= htmlspecialchars($guru['no_telpon']) ?></td>
                    <td class="px-4 py-2 border-b border-secondary-100 whitespace-nowrap">
                        <a href="teachers/details?id=<?= htmlspecialchars($guru['id']) ?>"
                           class="inline-flex items-center px-2 py-1 rounded border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 me-1"
                           title="Detail">
                            <iconify-icon icon="mdi:eye-outline" width="20" height="20"></iconify-icon>
                        </a>
                        <a href="teachers/edit?id=<?= htmlspecialchars($guru['id']) ?>"
                           class="inline-flex items-center px-2 py-1 rounded border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 me-1"
                           title="Edit">
                            <iconify-icon icon="mdi:pencil-outline" width="20" height="20"></iconify-icon>
                        </a>
                        <a href="teachers/delete?id=<?= htmlspecialchars($guru['id']) ?>"
                           class="inline-flex items-center px-2 py-1 rounded bg-status-error-500 text-white hover:bg-status-error-600"
                           title="Hapus">
                            <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center px-4 py-6 text-secondary-500">Tidak ada data guru ditemukan.</td>
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
