<?php
$pageTitle = "Detail Data Kelas";
$currentPage = 'classes';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$kelas = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT 
                    k.id, 
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    t.nama AS nama_tingkat,
                    g.nama AS nama_guru_wali,
                    g.nip AS nip_guru_wali
                FROM kelas k 
                JOIN tahun_ajaran ta ON k.id_tahun_ajaran = ta.id 
                JOIN tingkat t ON k.id_tingkat = t.id 
                LEFT JOIN guru g ON k.id_guru_wali = g.id 
                WHERE k.id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $kelas = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kelas) {
            $errorMessage = "Data kelas dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
} else {
    $errorMessage = "ID kelas tidak valid atau tidak diberikan.";
}

ob_start();
?>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-primary-700">Detail Data Kelas</h1>
        <div class="flex gap-2">
            <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
               class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                <iconify-icon icon="cil:arrow-left" width="20" height="20"></iconify-icon>
                Kembali ke Daftar Kelas
            </a>
            <?php if ($kelas): ?>
                <a href="../classes/edit?id=<?= htmlspecialchars($kelas['id']) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition">
                    <iconify-icon icon="cil:pencil" width="20" height="20"></iconify-icon>
                    Edit Data
                </a>
                <a href="../classes/delete?id=<?= htmlspecialchars($kelas['id']) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200 hover:bg-status-error-200 transition">
                    <iconify-icon icon="cil:trash" width="20" height="20"></iconify-icon>
                    Hapus Data
                </a>
            <?php endif; ?>
        </div>
    </div>

<?php if ($errorMessage): ?>
    <div class="flex items-center gap-2 mb-4 bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg">
        <iconify-icon icon="cil:warning" width="22"></iconify-icon>
        <span><?= htmlspecialchars($errorMessage) ?></span>
    </div>
<?php elseif ($kelas): ?>
    <div class="bg-white rounded-lg shadow border border-secondary-200 mb-4">
        <div class="px-6 py-4 border-b border-secondary-100 flex items-center gap-2">
            <iconify-icon icon="cil:info" width="22" class="text-primary-600"></iconify-icon>
            <h2 class="text-lg font-semibold text-primary-700 mb-0">Informasi Kelas</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">ID Kelas</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-secondary-800 border border-secondary-200"><?= htmlspecialchars($kelas['id']) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Nama Kelas</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-secondary-800 border border-secondary-200"><?= htmlspecialchars($kelas['nama_kelas']) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Tahun Ajaran</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-secondary-800 border border-secondary-200"><?= htmlspecialchars($kelas['nama_tahun_ajaran']) ?></div>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Tingkat</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-secondary-800 border border-secondary-200"><?= htmlspecialchars($kelas['nama_tingkat']) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Wali Kelas</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-secondary-800 border border-secondary-200">
                            <?php if ($kelas['nama_guru_wali']): ?>
                                <?= htmlspecialchars($kelas['nama_guru_wali']) ?>
                                <?php if ($kelas['nip_guru_wali']): ?>
                                    <span class="text-xs text-gray-500">(NIP: <?= htmlspecialchars($kelas['nip_guru_wali']) ?>)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <em class="text-gray-400">Belum ditentukan</em>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>