<?php
$pageTitle = "Tambah Data Guru";
$currentPage = 'teachers';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nip'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $no_telpon = $_POST['no_telpon'] ?? '';

    // Basic validation
    if (empty($nip) || empty($nama) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($no_telpon)) {
        $errorMessage = "Harap lengkapi semua kolom wajib (NIP, Nama, Tanggal Lahir, Jenis Kelamin, No. Telepon).";
    } else {
        try {
            $sql = "INSERT INTO guru (nip, nama, tanggal_lahir, jenis_kelamin, no_telpon)
                    VALUES (:nip, :nama, :tanggal_lahir, :jenis_kelamin, :no_telpon)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nip', $nip);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
            $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
            $stmt->bindParam(':no_telpon', $no_telpon);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/teachers');
                exit;
            } else {
                $errorMessage = "Gagal menambahkan data guru. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Tambah Data Guru</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Guru
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nip" name="nip" required value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                    <option value="1" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '1') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="0" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '0') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="no_telpon" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="no_telpon" name="no_telpon" required value="<?= htmlspecialchars($_POST['no_telpon'] ?? '') ?>">
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-save"></i> Simpan Data</button>
            <button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-repeat"></i> Reset Form</button>
        </div>
    </form>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>