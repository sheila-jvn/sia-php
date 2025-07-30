<?php
$pageTitle = "Edit Data Kelas";
$currentPage = 'classes';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';
$successMessage = '';
$kelas = null;


$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID Kelas tidak valid atau tidak diberikan.";
} else {

    try {
        $sql = "SELECT 
                    k.id, 
                    k.nama,
                    k.id_tahun_ajaran,
                    k.id_tingkat,
                    k.id_guru_wali,
                    ta.nama AS nama_tahun_ajaran,
                    t.nama AS nama_tingkat,
                    g.nama AS nama_guru_wali
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
            $errorMessage = "Data Kelas dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}


$tahunAjaran = [];
$tingkat = [];
$guru = [];

if (!$errorMessage) {
    try {

        $stmtTahun = $pdo->query("SELECT id, nama FROM tahun_ajaran ORDER BY nama");
        $tahunAjaran = $stmtTahun->fetchAll();
        
        $stmtTingkat = $pdo->query("SELECT id, nama FROM tingkat ORDER BY nama");
        $tingkat = $stmtTingkat->fetchAll();
        
        $stmtGuru = $pdo->query("SELECT id, nama FROM guru ORDER BY nama");
        $guru = $stmtGuru->fetchAll();
    } catch (PDOException $e) {
        $errorMessage = "Error loading dropdown data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $kelas) {
    $nama = $_POST['nama'] ?? '';
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_tingkat = $_POST['id_tingkat'] ?? '';
    $id_guru_wali = $_POST['id_guru_wali'] ?? '';

    if (empty($nama) || empty($id_tahun_ajaran) || empty($id_tingkat)) {
        $errorMessage = "Harap lengkapi semua kolom wajib (Nama Kelas, Tahun Ajaran, Tingkat).";
    } else {
        try {
            $sql = "UPDATE kelas SET 
                        nama = :nama, 
                        id_tahun_ajaran = :id_tahun_ajaran, 
                        id_tingkat = :id_tingkat, 
                        id_guru_wali = :id_guru_wali 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $stmt->bindParam(':id_tingkat', $id_tingkat);
            $stmt->bindParam(':id_guru_wali', $id_guru_wali);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/classes/details?id=' . $id);
                exit;
            } else {
                $errorMessage = "Gagal memperbarui data Kelas. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
    
    $kelas = array_merge($kelas, [
        'nama' => $nama,
        'id_tahun_ajaran' => $id_tahun_ajaran,
        'id_tingkat' => $id_tingkat,
        'id_guru_wali' => $id_guru_wali,
    ]);
}

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Data Kelas</h1>
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

    <?php if ($kelas): ?>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($kelas['id']) ?>">
        <div class="row g-3">
            <div class="col-md-12">
                <label for="nama" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" required 
                       placeholder="Contoh: XII IPA 1" 
                       value="<?= htmlspecialchars($kelas['nama'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label for="id_tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                    <option value="" disabled>Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" 
                                <?= (isset($kelas['id_tahun_ajaran']) && $kelas['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                <select class="form-select" id="id_tingkat" name="id_tingkat" required>
                    <option value="" disabled>Pilih Tingkat</option>
                    <?php foreach ($tingkat as $t): ?>
                        <option value="<?= $t['id'] ?>" 
                                <?= (isset($kelas['id_tingkat']) && $kelas['id_tingkat'] == $t['id']) ? 'selected' : '' ?>>
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
                                <?= (isset($kelas['id_guru_wali']) && $kelas['id_guru_wali'] == $g['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Guru wali kelas dapat dikosongkan jika belum ditentukan.</div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/classes/details?id=<?= htmlspecialchars($kelas['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x"></i> Batal
            </a>
        </div>
    </form>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>