<?php
$pageTitle = "Detail Data Absensi";
$currentPage = 'absensi'; 

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$absensi = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT 
                    kh.id,
                    kh.tanggal,
                    kh.keterangan,
                    s.nama AS nama_siswa,
                    s.nis AS nis_siswa,
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    ks.nama AS status_kehadiran
                FROM kehadiran kh
                JOIN siswa s ON kh.id_siswa = s.id
                JOIN kelas k ON kh.id_kelas = k.id
                JOIN tahun_ajaran ta ON kh.id_tahun_ajaran = ta.id
                JOIN kehadiran_status ks ON kh.id_status = ks.id
                WHERE kh.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $absensi = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$absensi) {
            $errorMessage = "Data absensi dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
} else {
    $errorMessage = "ID absensi tidak valid atau tidak diberikan.";
}

ob_start();
?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-primary-700">Detail Data Absensi</h1>
    <div class="flex gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
            <iconify-icon icon="cil:arrow-left" width="20"></iconify-icon>
            Kembali ke Daftar Absensi
        </a>
        <?php if ($absensi): ?>
            <a href="../absensi/edit?id=<?= htmlspecialchars($absensi['id']) ?>" class="inline-flex items-center gap-2 bg-primary-50 hover:bg-primary-100 text-primary-700 border border-primary-200 px-4 py-2 rounded-lg font-medium transition-colors">
                <iconify-icon icon="cil:pencil" width="20"></iconify-icon>
                Edit Data
            </a>
            <a href="../absensi/delete?id=<?= htmlspecialchars($absensi['id']) ?>" class="inline-flex items-center gap-2 bg-status-error-100 hover:bg-status-error-200 text-status-error-700 border border-status-error-200 px-4 py-2 rounded-lg font-medium transition-colors">
                <iconify-icon icon="cil:trash" width="20"></iconify-icon>
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
<?php elseif ($absensi): ?>
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <iconify-icon icon="cil:calendar-check" width="22" class="text-primary-600"></iconify-icon>
            <h2 class="text-lg font-semibold text-primary-700 mb-0">Informasi Absensi</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">ID Absensi</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200"><?= htmlspecialchars($absensi['id']) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Nama Siswa</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200">
                            <?= htmlspecialchars($absensi['nama_siswa']) ?>
                            <?php if ($absensi['nis_siswa']): ?>
                                <span class="text-xs text-gray-500 block">NIS: <?= htmlspecialchars($absensi['nis_siswa']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Kelas</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200"><?= htmlspecialchars($absensi['nama_kelas']) ?></div>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Tahun Ajaran</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200"><?= htmlspecialchars($absensi['nama_tahun_ajaran']) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Status Kehadiran</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200">
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
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
                                <?= htmlspecialchars($absensi['status_kehadiran']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Tanggal</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200">
                            <span class="font-semibold"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></span>
                            <span class="text-xs text-gray-500 block"><?= date('l', strtotime($absensi['tanggal'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($absensi['keterangan']): ?>
                <div class="grid grid-cols-1 mt-4">
                    <div>
                        <label class="block font-semibold text-primary-700 mb-1">Keterangan</label>
                        <div class="rounded-lg bg-secondary-50 px-3 py-2 text-gray-800 border border-gray-200 min-h-[80px]">
                            <?= nl2br(htmlspecialchars($absensi['keterangan'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>