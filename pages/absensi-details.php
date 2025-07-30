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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detail Data Absensi</h1>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Absensi
        </a>
        <?php if ($absensi): ?>
            <a href="../absensi/edit?id=<?= htmlspecialchars($absensi['id']) ?>" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil"></i> Edit Data
            </a>
            <a href="../absensi/delete?id=<?= htmlspecialchars($absensi['id']) ?>" class="btn btn-danger">
                <i class="bi bi-trash"></i> Hapus Data
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php elseif ($absensi): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-calendar-check"></i> Informasi Absensi
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">ID Absensi</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($absensi['id']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Nama Siswa</label>
                        <div class="form-control bg-light">
                            <?= htmlspecialchars($absensi['nama_siswa']) ?>
                            <?php if ($absensi['nis_siswa']): ?>
                                <small class="text-muted d-block">NIS: <?= htmlspecialchars($absensi['nis_siswa']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Kelas</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($absensi['nama_kelas']) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tahun Ajaran</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($absensi['nama_tahun_ajaran']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Status Kehadiran</label>
                        <div class="form-control bg-light">
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
                            <span class="badge <?= $badgeClass ?> fs-6"><?= htmlspecialchars($absensi['status_kehadiran']) ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tanggal</label>
                        <div class="form-control bg-light">
                            <span class="fw-bold"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></span>
                            <small class="text-muted d-block"><?= date('l', strtotime($absensi['tanggal'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($absensi['keterangan']): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Keterangan</label>
                            <div class="form-control bg-light" style="min-height: 80px;">
                                <?= nl2br(htmlspecialchars($absensi['keterangan'])) ?>
                            </div>
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