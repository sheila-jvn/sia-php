<?php
$pageTitle = "Tambah Data Kelas";
$currentPage = 'classes';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

try {
    $stmtTahun = $pdo->query("SELECT id, nama FROM tahun_ajaran ORDER BY nama");
    $tahunAjaran = $stmtTahun->fetchAll();
    
    $stmtTingkat = $pdo->query("SELECT id, nama FROM tingkat ORDER BY nama");
    $tingkat = $stmtTingkat->fetchAll();
    
    $stmtGuru = $pdo->query("SELECT id, nama FROM guru ORDER BY nama");
    $guru = $stmtGuru->fetchAll();
} catch (PDOException $e) {
    $errorMessage = "Error loading data: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_tingkat = $_POST['id_tingkat'] ?? '';
    $id_guru_wali = $_POST['id_guru_wali'] ?? '';
    $nama = $_POST['nama'] ?? '';

    if (empty($id_tahun_ajaran) || empty($id_tingkat) || empty($nama)) {
        $errorMessage = "Harap lengkapi semua kolom wajib (Tahun Ajaran, Tingkat, Nama Kelas).";
    } else {
        try {
            $sql = "INSERT INTO kelas (id_tahun_ajaran, id_tingkat, id_guru_wali, nama) 
                    VALUES (:id_tahun_ajaran, :id_tingkat, :id_guru_wali, :nama)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $stmt->bindParam(':id_tingkat', $id_tingkat);
            $stmt->bindParam(':id_guru_wali', $id_guru_wali);
            $stmt->bindParam(':nama', $nama);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/classes');
                exit;
            } else {
                $errorMessage = "Gagal menambahkan data kelas. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Tambah Data Kelas</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kelas
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
            <div class="col-md-12">
                <label for="nama" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" required 
                       placeholder="Contoh: XII IPA 1" 
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label for="id_tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                    <option value="" disabled selected>Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" 
                                <?= (isset($_POST['id_tahun_ajaran']) && $_POST['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                <select class="form-select" id="id_tingkat" name="id_tingkat" required>
                    <option value="" disabled selected>Pilih Tingkat</option>
                    <?php foreach ($tingkat as $t): ?>
                        <option value="<?= $t['id'] ?>" 
                                <?= (isset($_POST['id_tingkat']) && $_POST['id_tingkat'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-12">
                <label for="id_guru_wali" class="form-label">Guru Wali Kelas</label>
                <select class="form-select" id="id_guru_wali" name="id_guru_wali">
                    <option value="">Pilih Guru Wali (Opsional)</option>
                    <?php foreach ($guru as $g): ?>
                        <option value="<?= $g['id'] ?>" 
                                <?= (isset($_POST['id_guru_wali']) && $_POST['id_guru_wali'] == $g['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Guru wali kelas dapat diisi kemudian jika belum ditentukan.</div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-save"></i> Simpan Data
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="bi bi-arrow-repeat"></i> Reset Form
            </button>
        </div>
    </form>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>