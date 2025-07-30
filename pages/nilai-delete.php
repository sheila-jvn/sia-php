<?php
$pageTitle = "Hapus Data Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();
$errorMessage = '';
$successMessage = '';
$nilai = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID nilai tidak valid atau tidak diberikan.";
} else {
    try {
        $sql = "SELECT 
                    n.id,
                    n.nilai,
                    n.tanggal_penilaian,
                    n.keterangan,
                    s.nama AS nama_siswa,
                    s.nis AS nis_siswa,
                    mp.nama AS nama_mata_pelajaran,
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    nj.nama AS jenis_nilai
                FROM nilai n
                JOIN siswa s ON n.id_siswa = s.id
                JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
                JOIN kelas k ON n.id_kelas = k.id
                JOIN tahun_ajaran ta ON n.id_tahun_ajaran = ta.id
                JOIN nilai_jenis nj ON n.id_jenis_nilai = nj.id
                WHERE n.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $nilai = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$nilai) {
            $errorMessage = "Data nilai dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nilai) {
    try {
        $stmt = $pdo->prepare("DELETE FROM nilai WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: ' . htmlspecialchars($urlPrefix) . '/nilai?deleted=1');
            exit;
        } else {
            $errorMessage = "Gagal menghapus data nilai. Silakan coba lagi.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hapus Data Nilai</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Nilai
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php if ($nilai): ?>
            <div class="d-flex justify-content-end mt-3">
                <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Nilai
                </a>
            </div>
        <?php endif; ?>
    <?php elseif ($nilai): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($nilai['id']) ?>">
            <div class="mb-4">
                <h5>Apakah Anda yakin ingin menghapus data nilai berikut?</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Siswa:</strong> <?= htmlspecialchars($nilai['nama_siswa']) ?></p>
                                <p><strong>NIS:</strong> <?= htmlspecialchars($nilai['nis_siswa']) ?></p>
                                <p><strong>Mata Pelajaran:</strong> <?= htmlspecialchars($nilai['nama_mata_pelajaran']) ?></p>
                                <p><strong>Kelas:</strong> <?= htmlspecialchars($nilai['nama_kelas']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tahun Ajaran:</strong> <?= htmlspecialchars($nilai['nama_tahun_ajaran']) ?></p>
                                <p><strong>Jenis Nilai:</strong> 
                                    <span class="badge bg-info"><?= htmlspecialchars($nilai['jenis_nilai']) ?></span>
                                </p>
                                <p><strong>Nilai:</strong> 
                                    <span class="fw-bold fs-5 
                                        <?php 
                                        $nilaiNum = (float)$nilai['nilai'];
                                        if ($nilaiNum >= 80) echo 'text-success';
                                        elseif ($nilaiNum >= 70) echo 'text-warning';
                                        else echo 'text-danger';
                                        ?>">
                                        <?= htmlspecialchars($nilai['nilai']) ?>
                                    </span>
                                </p>
                                <p><strong>Tanggal Penilaian:</strong> <?= date('d/m/Y', strtotime($nilai['tanggal_penilaian'])) ?></p>
                            </div>
                        </div>
                        <?php if ($nilai['keterangan']): ?>
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Keterangan:</strong> <?= htmlspecialchars($nilai['keterangan']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <strong>Peringatan!</strong><br>
                        Data nilai yang dihapus tidak dapat dikembalikan. Pastikan Anda benar-benar ingin menghapus data ini.
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger me-2">
                    <i class="bi bi-trash"></i> Ya, Hapus Nilai
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="btn btn-outline-secondary">
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