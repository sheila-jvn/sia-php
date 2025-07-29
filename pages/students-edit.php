<?php
$pageTitle = "Edit Data Siswa";
$currentPage = 'students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';
$successMessage = '';
$student = null;

// Get student ID from GET or POST
$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID siswa tidak valid atau tidak diberikan.";
} else {
    // Fetch student data for GET or for repopulating after failed POST
    try {
        $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) {
            $errorMessage = "Data siswa dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $student) {
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
            $sql = "UPDATE siswa SET nis = :nis, nisn = :nisn, nama = :nama, no_kk = :no_kk, tanggal_lahir = :tanggal_lahir, jenis_kelamin = :jenis_kelamin, nama_ayah = :nama_ayah, nama_ibu = :nama_ibu, nik_ayah = :nik_ayah, nik_ibu = :nik_ibu, alamat = :alamat WHERE id = :id";
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
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/students');
                exit;
            } else {
                $errorMessage = "Gagal memperbarui data siswa. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
    // Repopulate $student for form after failed POST
    $student = array_merge($student, [
        'nis' => $nis,
        'nisn' => $nisn,
        'nama' => $nama,
        'no_kk' => $no_kk,
        'tanggal_lahir' => $tanggal_lahir,
        'jenis_kelamin' => $jenis_kelamin,
        'nama_ayah' => $nama_ayah,
        'nama_ibu' => $nama_ibu,
        'nik_ayah' => $nik_ayah,
        'nik_ibu' => $nik_ibu,
        'alamat' => $alamat,
    ]);
}

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Data Siswa</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($student): ?>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nis" name="nis" required value="<?= htmlspecialchars($student['nis'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nisn" name="nisn" required value="<?= htmlspecialchars($student['nisn'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($student['nama'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="no_kk" class="form-label">Nomor Kartu Keluarga</label>
                <input type="text" class="form-control" id="no_kk" name="no_kk" value="<?= htmlspecialchars($student['no_kk'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required value="<?= htmlspecialchars($student['tanggal_lahir'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="" disabled>Pilih Jenis Kelamin</option>
                    <option value="1" <?= (isset($student['jenis_kelamin']) && $student['jenis_kelamin'] == '1') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="0" <?= (isset($student['jenis_kelamin']) && $student['jenis_kelamin'] == '0') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($student['alamat'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label for="nama_ayah" class="form-label">Nama Ayah</label>
                <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="<?= htmlspecialchars($student['nama_ayah'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nik_ayah" class="form-label">NIK Ayah</label>
                <input type="text" class="form-control" id="nik_ayah" name="nik_ayah" value="<?= htmlspecialchars($student['nik_ayah'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="<?= htmlspecialchars($student['nama_ibu'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="nik_ibu" class="form-label">NIK Ibu</label>
                <input type="text" class="form-control" id="nik_ibu" name="nik_ibu" value="<?= htmlspecialchars($student['nik_ibu'] ?? '') ?>">
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-save"></i> Simpan Perubahan</button>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/students/details?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i> Batal</a>
        </div>
    </form>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
