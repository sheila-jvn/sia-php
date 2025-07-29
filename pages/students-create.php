<?php
$pageTitle = "Tambah Data Siswa";
$currentPage = 'students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $nisn = $_POST['nisn'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $no_kk = $_POST['no_kk'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $nama_ayah = $_POST['nama_ayah'] ?? '';
    $nama_ibu = $_POST['nama_ibu'] ?? '';
    $nik_ayah = $_POST['nik_ayah'] ?? '';
    $nik_ibu = $_POST['nik_ibu'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    // Basic validation
    if (empty($nis) || empty($nisn) || empty($nama) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($alamat)) {
        $errorMessage = "Harap lengkapi semua kolom wajib (NIS, NISN, Nama, Tanggal Lahir, Jenis Kelamin, Alamat).";
    } else {
        try {
            $sql = "INSERT INTO siswa (nis, nisn, nama, no_kk, tanggal_lahir, jenis_kelamin, nama_ayah, nama_ibu, nik_ayah, nik_ibu, alamat)
                    VALUES (:nis, :nisn, :nama, :no_kk, :tanggal_lahir, :jenis_kelamin, :nama_ayah, :nama_ibu, :nik_ayah, :nik_ibu, :alamat)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nis', $nis);
            $stmt->bindParam(':nisn', $nisn);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':no_kk', $no_kk);
            $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
            $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
            $stmt->bindParam(':nama_ayah', $nama_ayah);
            $stmt->bindParam(':nama_ibu', $nama_ibu);
            $stmt->bindParam(':nik_ayah', $nik_ayah);
            $stmt->bindParam(':nik_ibu', $nik_ibu);
            $stmt->bindParam(':alamat', $alamat);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/students');
                exit;
            } else {
                $errorMessage = "Gagal menambahkan data siswa. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    :root {
        --primary-blue: #2c5282;
        --light-blue: #ebf8ff;
        --text-color: #4a5568;
        --border-color: #cbd5e0;
        --bg-color: #f7fafc;
        --success-green: #48bb78;
        --danger-red: #f56565;
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.25);
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

    .card {
        border-color: var(--border-color);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for cards */
        border-radius: 0.75rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-color);
    }

    .alert-success {
        background-color: var(--success-green);
        color: white;
        border-color: var(--success-green);
    }

    .alert-danger {
        background-color: var(--danger-red);
        color: white;
        border-color: var(--danger-red);
    }
    .form-label.required::after {
        content: " *";
        color: var(--danger-red);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Tambah Data Siswa</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Siswa
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
                <label for="nis" class="form-label required">NIS</label>
                <input type="text" class="form-control" id="nis" name="nis" required value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nisn" class="form-label required">NISN</label>
                <input type="text" class="form-control" id="nisn" name="nisn" required value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label for="nama" class="form-label required">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="no_kk" class="form-label">Nomor Kartu Keluarga</label>
                <input type="text" class="form-control" id="no_kk" name="no_kk" value="<?= htmlspecialchars($_POST['no_kk'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="tanggal_lahir" class="form-label required">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="jenis_kelamin" class="form-label required">Jenis Kelamin</label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                    <option value="1" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '1') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="0" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '0') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="alamat" class="form-label required">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label for="nama_ayah" class="form-label">Nama Ayah</label>
                <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="<?= htmlspecialchars($_POST['nama_ayah'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nik_ayah" class="form-label">NIK Ayah</label>
                <input type="text" class="form-control" id="nik_ayah" name="nik_ayah" value="<?= htmlspecialchars($_POST['nik_ayah'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="<?= htmlspecialchars($_POST['nama_ibu'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nik_ibu" class="form-label">NIK Ibu</label>
                <input type="text" class="form-control" id="nik_ibu" name="nik_ibu" value="<?= htmlspecialchars($_POST['nik_ibu'] ?? '') ?>">
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save"></i> Simpan Data</button>
            <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset Form</button>
        </div>
    </form>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>