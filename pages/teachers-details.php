<?php
$pageTitle = "Detail Data Guru";
$currentPage = 'teachers';

require_once __DIR__ . '/../lib/database.php'; // Adjust path if necessary

$pdo = getDbConnection();

$teacher = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT * FROM guru WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            $errorMessage = "Data Guru dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
} else {
    $errorMessage = "ID guru tidak valid atau tidak diberikan.";
}

ob_start(); // Start output buffering
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detail Data Guru</h1>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Guru
        </a>
        <?php if ($teacher): // Only show edit button if teacher data is found ?>
            <a href="teachers/edit?id=<?= htmlspecialchars($teacher['id']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit Data
            </a>
        <?php endif; ?>
            <?php if ($teacher): ?>
                <a href="teachers/delete?id=<?= htmlspecialchars($teacher['id']) ?>" class="btn btn-danger ms-2">
                    <i class="bi bi-trash"></i> Hapus Data
                </a>
            <?php endif; ?>
    </div>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php elseif ($teacher): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">ID Guru</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($teacher['id']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">NIP (Nomor Induk Pegawai)</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($teacher['nip']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Nama Lengkap</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($teacher['nama']) ?></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Tanggal Lahir</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($teacher['tanggal_lahir']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Jenis Kelamin</div>
                    <div class="form-control bg-light"><?= $teacher['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">No. Telepon</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($teacher['no_telpon'] ?: '-') ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>