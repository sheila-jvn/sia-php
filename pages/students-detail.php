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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /*
     * Make sure these :root variables are defined either here or
     * preferably in a shared CSS file included by your _layout.php.
     * If they are already in _layout.php's CSS, you can remove them from here.
     */
    :root {
        --primary-blue: #2c5282;
        --light-blue: #ebf8ff;
        --text-color: #4a5568;
        --border-color: #cbd5e0;
        --bg-color: #f7fafc;
        --secondary-text-color: #718096; /* For less prominent text */
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }

    .card {
        border-color: var(--border-color);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for cards */
        border-radius: 0.75rem;
    }

    .btn-primary {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #2a4365;
        border-color: #2a4365;
    }

    .btn-secondary {
        background-color: #a0aec0;
        border-color: #a0aec0;
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-secondary:hover {
        background-color: #718096;
        border-color: #718096;
    }

    .btn-warning { /* Re-adding warning for consistency if edit button is present */
        background-color: #f6ad55;
        border-color: #f6ad55;
        color: #fff;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-warning:hover {
        background-color: #ed8936;
        border-color: #ed8936;
    }

    .detail-item {
        margin-bottom: 1rem;
    }

    .detail-item strong {
        color: var(--primary-blue); /* Highlight labels */
        display: block; /* Make label appear on its own line */
        margin-bottom: 0.25rem;
        font-size: 0.9rem; /* Slightly smaller label text */
    }

    .detail-item span {
        display: block; /* Make value appear on its own line */
        padding: 0.5rem 0.75rem;
        background-color: var(--light-blue);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        word-wrap: break-word; /* Ensure long text wraps */
    }

    .alert-danger {
        background-color: #f56565;
        color: white;
        border-color: #f56565;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detail Data Siswa</h1>
    <div>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Siswa
        </a>
        <?php if ($student): // Only show edit button if student data is found ?>
            <a href="edit_student.php?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Data
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
                <div class="detail-item">
                    <strong>ID Siswa</strong>
                    <span><?= htmlspecialchars($student['id']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>NIS (Nomor Induk Siswa)</strong>
                    <span><?= htmlspecialchars($student['nis']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>NISN (Nomor Induk Siswa Nasional)</strong>
                    <span><?= htmlspecialchars($student['nisn']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Nama Lengkap</strong>
                    <span><?= htmlspecialchars($student['nama']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Nomor Kartu Keluarga</strong>
                    <span><?= htmlspecialchars($student['no_kk'] ?: '-') ?></span> </div>
                <div class="detail-item">
                    <strong>Tanggal Lahir</strong>
                    <span><?= htmlspecialchars($student['tanggal_lahir']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Jenis Kelamin</strong>
                    <span><?= $student['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="detail-item">
                    <strong>Alamat</strong>
                    <span><?= nl2br(htmlspecialchars($student['alamat'])) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Nama Ayah</strong>
                    <span><?= htmlspecialchars($student['nama_ayah'] ?: '-') ?></span>
                </div>
                <div class="detail-item">
                    <strong>NIK Ayah</strong>
                    <span><?= htmlspecialchars($student['nik_ayah'] ?: '-') ?></span>
                </div>
                <div class="detail-item">
                    <strong>Nama Ibu</strong>
                    <span><?= htmlspecialchars($student['nama_ibu'] ?: '-') ?></span>
                </div>
                <div class="detail-item">
                    <strong>NIK Ibu</strong>
                    <span><?= htmlspecialchars($student['nik_ibu'] ?: '-') ?></span>
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