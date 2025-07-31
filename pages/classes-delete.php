<?php
$pageTitle = "Hapus Data Kelas";
$currentPage = 'classes';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();
$errorMessage = '';
$successMessage = '';
$kelas = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID kelas tidak valid atau tidak diberikan.";
} else {
    try {
        $sql = "SELECT 
                    k.id, 
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    t.nama AS nama_tingkat,
                    g.nama AS nama_guru_wali
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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $kelas) {
    try {
        // Check for related kehadiran (attendance) records
        $checkKehadiran = $pdo->prepare("SELECT COUNT(*) as count FROM kehadiran WHERE id_kelas = :id");
        $checkKehadiran->bindParam(':id', $id, PDO::PARAM_INT);
        $checkKehadiran->execute();
        $kehadiranCount = $checkKehadiran->fetch(PDO::FETCH_ASSOC)['count'];

        // Check for related nilai (grades) records
        $checkNilai = $pdo->prepare("SELECT COUNT(*) as count FROM nilai WHERE id_kelas = :id");
        $checkNilai->bindParam(':id', $id, PDO::PARAM_INT);
        $checkNilai->execute();
        $nilaiCount = $checkNilai->fetch(PDO::FETCH_ASSOC)['count'];

        if ($kehadiranCount > 0) {
            $errorMessage = "Tidak dapat menghapus kelas ini karena masih ada $kehadiranCount data kehadiran (absensi) terkait kelas ini. Silakan hapus data kehadiran terlebih dahulu.";
        } elseif ($nilaiCount > 0) {
            $errorMessage = "Tidak dapat menghapus kelas ini karena masih ada $nilaiCount data nilai terkait kelas ini. Silakan hapus data nilai terlebih dahulu.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM kelas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/classes?deleted=1');
                exit;
            } else {
                $errorMessage = "Gagal menghapus data kelas. Silakan coba lagi.";
            }
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

ob_start();
?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-primary-700">Hapus Data Kelas</h1>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="cil:arrow-left" width="20" height="20"></iconify-icon>
            Kembali ke Daftar Kelas
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border border-secondary-200">
        <?php if ($errorMessage): ?>
            <div class="flex items-center gap-2 mb-4 bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg">
                <iconify-icon icon="cil:warning" width="22"></iconify-icon>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
            <?php if ($kelas): ?>
                <div class="flex justify-end mt-3">
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
<iconify-icon icon="cil:arrow-left" width="20" height="20"></iconify-icon>                        Kembali ke Daftar Kelas
                    </a>
                </div>
            <?php endif; ?>
        <?php elseif ($kelas): ?>
            <form method="POST" action="" class="space-y-6">
                <input type="hidden" name="id" value="<?= htmlspecialchars($kelas['id']) ?>">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-status-error-700 mb-2">Apakah Anda yakin ingin menghapus data
                        kelas berikut?</h2>
                    <div class="bg-secondary-50 rounded-lg border border-secondary-200 p-4 mt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p>
                                    <span class="font-semibold">Nama Kelas:</span> <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                </p>
                                <p>
                                    <span class="font-semibold">Tahun Ajaran:</span> <?= htmlspecialchars($kelas['nama_tahun_ajaran']) ?>
                                </p>
                            </div>
                            <div>
                                <p>
                                    <span class="font-semibold">Tingkat:</span> <?= htmlspecialchars($kelas['nama_tingkat']) ?>
                                </p>
                                <p>
                                    <span class="font-semibold">Guru Wali:</span> <?= $kelas['nama_guru_wali'] ? htmlspecialchars($kelas['nama_guru_wali']) : '<em class=\'text-gray-400\'>Belum ditentukan</em>' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-4 bg-status-warning-100 border border-status-warning-200 text-status-warning-700 px-4 py-3 rounded-lg">
<iconify-icon icon="cil:warning" width="20" height="20"></iconify-icon>                        <div>
                            <span class="font-semibold">Peringatan!</span><br>
                            Data yang dihapus tidak dapat dikembalikan. Pastikan tidak ada siswa yang terdaftar di kelas
                            ini sebelum menghapusnya.
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap justify-end gap-2 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition">
                        <iconify-icon icon="cil:trash" width="20" height="20"></iconify-icon>
                        Ya, Hapus Kelas
                    </button>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                        <iconify-icon icon="cil:x" width="20" height="20"></iconify-icon>
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