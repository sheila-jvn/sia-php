<?php
$pageTitle = "Detail Data Nilai";
$currentPage = 'nilai'; 

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$nilai = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

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
} else {
    $errorMessage = "ID nilai tidak valid atau tidak diberikan.";
}

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detail Data Nilai</h1>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Nilai
        </a>
        <?php if ($nilai): ?>
            <a href="../nilai/edit?id=<?= htmlspecialchars($nilai['id']) ?>" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil"></i> Edit Data
            </a>
            <a href="../nilai/delete?id=<?= htmlspecialchars($nilai['id']) ?>" class="btn btn-danger">
                <i class="bi bi-trash"></i> Hapus Data
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php elseif ($nilai): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-clipboard-data"></i> Informasi Nilai
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">ID Nilai</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($nilai['id']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Nama Siswa</label>
                        <div class="form-control bg-light">
                            <?= htmlspecialchars($nilai['nama_siswa']) ?>
                            <?php if ($nilai['nis_siswa']): ?>
                                <small class="text-muted d-block">NIS: <?= htmlspecialchars($nilai['nis_siswa']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Mata Pelajaran</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($nilai['nama_mata_pelajaran']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Kelas</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($nilai['nama_kelas']) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tahun Ajaran</label>
                        <div class="form-control bg-light"><?= htmlspecialchars($nilai['nama_tahun_ajaran']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Jenis Nilai</label>
                        <div class="form-control bg-light">
                            <span class="badge bg-info"><?= htmlspecialchars($nilai['jenis_nilai']) ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Nilai</label>
                        <div class="form-control bg-light">
                            <span class="fw-bold fs-4 
                                <?php 
                                $nilaiNum = (float)$nilai['nilai'];
                                if ($nilaiNum >= 80) echo 'text-success';
                                elseif ($nilaiNum >= 70) echo 'text-warning';
                                else echo 'text-danger';
                                ?>">
                                <?= htmlspecialchars($nilai['nilai']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tanggal Penilaian</label>
                        <div class="form-control bg-light"><?= date('d/m/Y', strtotime($nilai['tanggal_penilaian'])) ?></div>
                    </div>
                </div>
            </div>
            
            <?php if ($nilai['keterangan']): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Keterangan</label>
                            <div class="form-control bg-light" style="min-height: 80px;">
                                <?= nl2br(htmlspecialchars($nilai['keterangan'])) ?>
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