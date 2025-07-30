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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hapus Data Kelas</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kelas
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php if ($kelas): ?>
            <div class="d-flex justify-content-end mt-3">
                <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kelas
                </a>
            </div>
        <?php endif; ?>
    <?php elseif ($kelas): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($kelas['id']) ?>">
            <div class="mb-4">
                <h5>Apakah Anda yakin ingin menghapus data kelas berikut?</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Kelas:</strong> <?= htmlspecialchars($kelas['nama_kelas']) ?></p>
                                <p><strong>Tahun Ajaran:</strong> <?= htmlspecialchars($kelas['nama_tahun_ajaran']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tingkat:</strong> <?= htmlspecialchars($kelas['nama_tingkat']) ?></p>
                                <p><strong>Guru Wali:</strong> 
                                    <?= $kelas['nama_guru_wali'] ? htmlspecialchars($kelas['nama_guru_wali']) : '<em class="text-muted">Belum ditentukan</em>' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <strong>Peringatan!</strong><br>
                        Data yang dihapus tidak dapat dikembalikan. Pastikan tidak ada siswa yang terdaftar di kelas ini sebelum menghapusnya.
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger me-2">
                    <i class="bi bi-trash"></i> Ya, Hapus Kelas
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i> Batal
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