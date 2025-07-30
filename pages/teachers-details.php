<?php
$pageTitle = "Detail Data Guru";
$currentPage = 'teachers';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$teacher = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "SELECT * FROM guru WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$teacher) {
            $errorMessage = "Data Guru dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
} else {
    $errorMessage = "ID guru tidak valid atau tidak diberikan.";
}

ob_start();
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-2">
    <h1 class="text-2xl font-bold text-primary-700 mb-2 md:mb-0">Detail Data Guru</h1>
    <div class="flex flex-row gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers"
           class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 rounded">
            <iconify-icon icon="mdi:arrow-left" width="20" height="20" class="mr-1"></iconify-icon>
            Kembali ke Daftar Guru
        </a>
        <?php if ($teacher): ?>
            <a href="teachers/edit?id=<?= htmlspecialchars($teacher['id']) ?>"
               class="inline-flex items-center px-4 py-2 bg-status-warning-100 text-status-warning-700 hover:bg-status-warning-200 rounded">
                <iconify-icon icon="mdi:pencil-outline" width="20" height="20" class="mr-1"></iconify-icon>
                Edit Data
            </a>
            <a href="teachers/delete?id=<?= htmlspecialchars($teacher['id']) ?>"
               class="inline-flex items-center px-4 py-2 bg-status-error-500 text-white hover:bg-status-error-600 rounded">
                <iconify-icon icon="mdi:trash-can-outline" width="20" height="20" class="mr-1"></iconify-icon>
                Hapus Data
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded shadow p-6">
    <?php if ($errorMessage): ?>
        <div class="mb-4 px-4 py-3 rounded bg-status-error-100 text-status-error-700 border border-status-error-200">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php elseif ($teacher): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <div class="text-xs font-semibold text-primary-600 mb-1">ID Guru</div>
                    <div class="px-3 py-2 rounded bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($teacher['id']) ?></div>
                </div>
                <div class="mb-4">
                    <div class="text-xs font-semibold text-primary-600 mb-1">NIP (Nomor Induk Pegawai)</div>
                    <div class="px-3 py-2 rounded bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($teacher['nip']) ?></div>
                </div>
                <div class="mb-4">
                    <div class="text-xs font-semibold text-primary-600 mb-1">Nama Lengkap</div>
                    <div class="px-3 py-2 rounded bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($teacher['nama']) ?></div>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <div class="text-xs font-semibold text-primary-600 mb-1">Tanggal Lahir</div>
                    <div class="px-3 py-2 rounded bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($teacher['tanggal_lahir']) ?></div>
                </div>
                <div class="mb-4">
                    <div class="text-xs font-semibold text-primary-600 mb-1">Jenis Kelamin</div>
                    <div class="px-3 py-2 rounded bg-secondary-50 border border-secondary-200"><?= $teacher['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></div>
                </div>
                <div class="mb-4">
                    <div class="text-xs font-semibold text-primary-600 mb-1">No. Telepon</div>
                    <div class="px-3 py-2 rounded bg-secondary-50 border border-secondary-200"><?= htmlspecialchars($teacher['no_telpon'] ?: '-') ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
