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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detail Data Kelas</h1>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kelas
        </a>
        <?php if ($kelas): ?>
            <a href="../classes/edit?id=<?= htmlspecialchars($kelas['id']) ?>" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil"></i> Edit Data
            </a>
            <a href="../classes/delete?id=<?= htmlspecialchars($kelas['id']) ?>" class="btn btn-danger">
                <i class="bi bi-trash"></i> Hapus Data
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php elseif ($kelas): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-info-circle"></i> Informasi Kelas
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">ID Kelas</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($kelas['id']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Nama Kelas</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($kelas['nama_kelas']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tahun Ajaran</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($kelas['nama_tahun_ajaran']) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tingkat</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($kelas['nama_tingkat']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Wali Kelas</label>
                        <div class="form-control bg-light">
                            <?php if ($kelas['nama_guru_wali']): ?>
                                <?= htmlspecialchars($kelas['nama_guru_wali']) ?>
                                <?php if ($kelas['nip_guru_wali']): ?>
                                    <small class="text-muted">(NIP: <?= htmlspecialchars($kelas['nip_guru_wali']) ?>)</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <em class="text-muted">Belum ditentukan</em>
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