<?php
$pageTitle = "Detail Data Siswa";
$currentPage = 'students'; // Keep 'students' active for navigation

require_once __DIR__ . '/../lib/database.php'; // Adjust path if necessary

$pdo = getDbConnection();

$student = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT * FROM siswa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            $errorMessage = "Data siswa dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
} else {
    $errorMessage = "ID siswa tidak valid atau tidak diberikan.";
}

ob_start(); // Start output buffering
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detail Data Siswa</h1>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa
        </a>
        <?php if ($student): // Only show edit button if student data is found ?>
            <a href="students/edit?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit Data
            </a>
        <?php endif; ?>
            <?php if ($student): ?>
                <a href="students/delete?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-danger ms-2">
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
    <?php elseif ($student): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">ID Siswa</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['id']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">NIS (Nomor Induk Siswa)</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nis']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">NISN (Nomor Induk Siswa Nasional)</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nisn']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Nama Lengkap</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nama']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Nomor Kartu Keluarga</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['no_kk'] ?: '-') ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Tanggal Lahir</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['tanggal_lahir']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Jenis Kelamin</div>
                    <div class="form-control bg-light"><?= $student['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Alamat</div>
                    <div class="form-control bg-light" style="white-space: pre-line;"><?= htmlspecialchars($student['alamat']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Nama Ayah</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nama_ayah'] ?: '-') ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">NIK Ayah</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nik_ayah'] ?: '-') ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">Nama Ibu</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nama_ibu'] ?: '-') ?></div>
                </div>
                <div class="mb-3">
                    <div class="fw-bold text-primary small mb-1">NIK Ibu</div>
                    <div class="form-control bg-light"><?= htmlspecialchars($student['nik_ibu'] ?: '-') ?></div>
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