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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hapus Data Absensi</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Absensi
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php if ($absensi): ?>
            <div class="d-flex justify-content-end mt-3">
                <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Absensi
                </a>
            </div>
        <?php endif; ?>
    <?php elseif ($absensi): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($absensi['id']) ?>">
            <div class="mb-4">
                <h5>Apakah Anda yakin ingin menghapus data absensi berikut?</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Siswa:</strong> <?= htmlspecialchars($absensi['nama_siswa']) ?></p>
                                <p><strong>NIS:</strong> <?= htmlspecialchars($absensi['nis_siswa']) ?></p>
                                <p><strong>Kelas:</strong> <?= htmlspecialchars($absensi['nama_kelas']) ?></p>
                                <p><strong>Tahun Ajaran:</strong> <?= htmlspecialchars($absensi['nama_tahun_ajaran']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status Kehadiran:</strong> 
                                    <?php 
                                    $status = strtolower($absensi['status_kehadiran']);
                                    $badgeClass = 'bg-secondary';
                                    if (strpos($status, 'hadir') !== false) {
                                        $badgeClass = 'bg-success';
                                    } elseif (strpos($status, 'tidak hadir') !== false || strpos($status, 'alpha') !== false) {
                                        $badgeClass = 'bg-danger';
                                    } elseif (strpos($status, 'izin') !== false) {
                                        $badgeClass = 'bg-warning';
                                    } elseif (strpos($status, 'sakit') !== false) {
                                        $badgeClass = 'bg-info';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($absensi['status_kehadiran']) ?></span>
                                </p>
                                <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></p>
                                <?php if ($absensi['keterangan']): ?>
                                    <p><strong>Keterangan:</strong> <?= htmlspecialchars($absensi['keterangan']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <strong>Peringatan!</strong><br>
                        Data absensi yang dihapus tidak dapat dikembalikan. Pastikan Anda benar-benar ingin menghapus data ini.
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger me-2">
                    <i class="bi bi-trash"></i> Ya, Hapus Absensi
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="btn btn-outline-secondary">
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