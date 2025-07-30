<?php
$pageTitle = "Tambah Data Absensi";
$currentPage = 'absensi';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

try {
    $stmtSiswa = $pdo->query("SELECT id, nama FROM siswa ORDER BY nama");
    $siswa = $stmtSiswa->fetchAll();
    
    $stmtKelas = $pdo->query("SELECT id, nama FROM kelas ORDER BY nama");
    $kelas = $stmtKelas->fetchAll();
    
    $stmtTahun = $pdo->query("SELECT id, nama FROM tahun_ajaran ORDER BY nama");
    $tahunAjaran = $stmtTahun->fetchAll();
    
    $stmtStatus = $pdo->query("SELECT id, nama FROM kehadiran_status ORDER BY nama");
    $statusKehadiran = $stmtStatus->fetchAll();
    
} catch (PDOException $e) {
    $errorMessage = "Error loading data: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = $_POST['id_siswa'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_status = $_POST['id_status'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if (empty($id_siswa) || empty($id_kelas) || empty($id_tahun_ajaran) || 
        empty($id_status) || empty($tanggal)) {
        $errorMessage = "Harap lengkapi semua kolom wajib.";
    } else {
        try {
            $checkSql = "SELECT COUNT(*) as count FROM kehadiran 
                        WHERE id_siswa = :id_siswa 
                        AND id_kelas = :id_kelas 
                        AND id_tahun_ajaran = :id_tahun_ajaran 
                        AND tanggal = :tanggal";
            
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':id_siswa', $id_siswa);
            $checkStmt->bindParam(':id_kelas', $id_kelas);
            $checkStmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $checkStmt->bindParam(':tanggal', $tanggal);
            $checkStmt->execute();
            
            $existingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($existingCount > 0) {
                $errorMessage = "Data absensi untuk siswa ini pada tanggal yang sama sudah ada.";
            } else {
                $sql = "INSERT INTO kehadiran (id_siswa, id_kelas, id_tahun_ajaran, id_status, tanggal, keterangan) 
                        VALUES (:id_siswa, :id_kelas, :id_tahun_ajaran, :id_status, :tanggal, :keterangan)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_siswa', $id_siswa);
                $stmt->bindParam(':id_kelas', $id_kelas);
                $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
                $stmt->bindParam(':id_status', $id_status);
                $stmt->bindParam(':tanggal', $tanggal);
                $stmt->bindParam(':keterangan', $keterangan);

                if ($stmt->execute()) {
                    header('Location: ' . htmlspecialchars($urlPrefix) . '/absensi');
                    exit;
                } else {
                    $errorMessage = "Gagal menambahkan data absensi. Silakan coba lagi.";
                }
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Tambah Data Absensi</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Absensi
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
                <label for="id_siswa" class="form-label">Siswa <span class="text-danger">*</span></label>
                <select class="form-select" id="id_siswa" name="id_siswa" required>
                    <option value="" disabled selected>Pilih Siswa</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s['id'] ?>" 
                                <?= (isset($_POST['id_siswa']) && $_POST['id_siswa'] == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                <select class="form-select" id="id_kelas" name="id_kelas" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" 
                                <?= (isset($_POST['id_kelas']) && $_POST['id_kelas'] == $k['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
                <label for="id_status" class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_status" name="id_status" required>
                    <option value="" disabled selected>Pilih Status Kehadiran</option>
                    <?php foreach ($statusKehadiran as $st): ?>
                        <option value="<?= $st['id'] ?>" 
                                <?= (isset($_POST['id_status']) && $_POST['id_status'] == $st['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-12">
                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required 
                       value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>">
                <div class="form-text">Pilih tanggal kehadiran siswa</div>
            </div>
            
            <div class="col-md-12">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                          placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                <div class="form-text">Contoh: Sakit demam, Izin keperluan keluarga, dll.</div>
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