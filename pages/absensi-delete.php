<?php
$pageTitle = "Hapus Data Absensi";
$currentPage = 'absensi';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();
$errorMessage = '';
$successMessage = '';
$absensi = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID absensi tidak valid atau tidak diberikan.";
} else {
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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $absensi) {
    try {
        $stmt = $pdo->prepare("DELETE FROM kehadiran WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: ' . htmlspecialchars($urlPrefix) . '/absensi?deleted=1');
            exit;
        } else {
            $errorMessage = "Gagal menghapus data absensi. Silakan coba lagi.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

ob_start();
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-primary-700">Hapus Data Absensi</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
        <iconify-icon icon="cil:arrow-left" width="20"></iconify-icon>
        Kembali ke Daftar Absensi
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 border border-gray-200">
    <?php if ($errorMessage): ?>
        <div class="flex items-center gap-2 mb-4 bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg">
            <iconify-icon icon="cil:warning" width="22"></iconify-icon>
            <span><?= htmlspecialchars($errorMessage) ?></span>
        </div>
        <?php if ($absensi): ?>
            <div class="flex justify-end mt-3">
                <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
                    <iconify-icon icon="cil:arrow-left" width="20"></iconify-icon>
                    Kembali ke Daftar Absensi
                </a>
            </div>
        <?php endif; ?>
    <?php elseif ($absensi): ?>
        <form method="POST" action="" class="space-y-6">
            <input type="hidden" name="id" value="<?= htmlspecialchars($absensi['id']) ?>">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-status-error-700 mb-2">Apakah Anda yakin ingin menghapus data absensi berikut?</h2>
                <div class="bg-secondary-50 rounded-lg border border-gray-200 p-4 mt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><span class="font-semibold">Siswa:</span> <?= htmlspecialchars($absensi['nama_siswa']) ?></p>
                            <p><span class="font-semibold">NIS:</span> <?= htmlspecialchars($absensi['nis_siswa']) ?></p>
                            <p><span class="font-semibold">Kelas:</span> <?= htmlspecialchars($absensi['nama_kelas']) ?></p>
                            <p><span class="font-semibold">Tahun Ajaran:</span> <?= htmlspecialchars($absensi['nama_tahun_ajaran']) ?></p>
                        </div>
                        <div>
                            <p><span class="font-semibold">Status Kehadiran:</span> 
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
                            </p>
                            <p><span class="font-semibold">Tanggal:</span> <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></p>
                            <?php if ($absensi['keterangan']): ?>
                                <p><span class="font-semibold">Keterangan:</span> <?= htmlspecialchars($absensi['keterangan']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4 bg-status-warning-100 border border-status-warning-200 text-status-warning-700 px-4 py-3 rounded-lg">
                    <iconify-icon icon="cil:warning" width="22"></iconify-icon>
                    <div>
                        <span class="font-semibold">Peringatan!</span><br>
                        Data absensi yang dihapus tidak dapat dikembalikan. Pastikan Anda benar-benar ingin menghapus data ini.
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap justify-end gap-2 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 bg-status-error-600 hover:bg-status-error-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    <iconify-icon icon="cil:trash" width="20"></iconify-icon>
                    Ya, Hapus Absensi
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
                    <iconify-icon icon="cil:x" width="20"></iconify-icon>
                    Batal
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>