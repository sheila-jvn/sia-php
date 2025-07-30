<?php
$pageTitle = "Edit Data Absensi";
$currentPage = 'absensi';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';
$successMessage = '';
$absensi = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID absensi tidak valid atau tidak diberikan.";
} else {
    try {
        $sql = "SELECT 
                    kh.id,
                    kh.id_siswa,
                    kh.id_kelas,
                    kh.id_tahun_ajaran,
                    kh.id_status,
                    kh.tanggal,
                    kh.keterangan,
                    s.nama AS nama_siswa,
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    ks.nama AS status_kehadiran
                FROM kehadiran kh
                JOIN siswa s ON kh.id_siswa = s.id
                JOIN kelas k ON kh.id_kelas = k.id
                JOIN tahun_ajaran ta ON kh.id_tahun_ajaran = ta.id
                JOIN kehadiran_status ks ON kh.id_status = ks.id
                WHERE kh.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $absensi = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$absensi) {
            $errorMessage = "Data absensi dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

$siswa = [];
$kelas = [];
$tahunAjaran = [];
$statusKehadiran = [];

if (!$errorMessage) {
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
        $errorMessage = "Error loading dropdown data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $absensi) {
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
                        AND tanggal = :tanggal
                        AND id != :current_id";
            
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':id_siswa', $id_siswa);
            $checkStmt->bindParam(':id_kelas', $id_kelas);
            $checkStmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $checkStmt->bindParam(':tanggal', $tanggal);
            $checkStmt->bindParam(':current_id', $id);
            $checkStmt->execute();
            
            $existingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($existingCount > 0) {
                $errorMessage = "Data absensi untuk siswa ini pada tanggal yang sama sudah ada.";
            } else {
                $sql = "UPDATE kehadiran SET 
                            id_siswa = :id_siswa,
                            id_kelas = :id_kelas,
                            id_tahun_ajaran = :id_tahun_ajaran,
                            id_status = :id_status,
                            tanggal = :tanggal,
                            keterangan = :keterangan
                        WHERE id = :id";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_siswa', $id_siswa);
                $stmt->bindParam(':id_kelas', $id_kelas);
                $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
                $stmt->bindParam(':id_status', $id_status);
                $stmt->bindParam(':tanggal', $tanggal);
                $stmt->bindParam(':keterangan', $keterangan);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    header('Location: ' . htmlspecialchars($urlPrefix) . '/absensi/details?id=' . $id);
                    exit;
                } else {
                    $errorMessage = "Gagal memperbarui data absensi. Silakan coba lagi.";
                }
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
    
    $absensi = array_merge($absensi, [
        'id_siswa' => $id_siswa,
        'id_kelas' => $id_kelas,
        'id_tahun_ajaran' => $id_tahun_ajaran,
        'id_status' => $id_status,
        'tanggal' => $tanggal,
        'keterangan' => $keterangan,
    ]);
}

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Data Absensi</h1>
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

    <?php if ($absensi): ?>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($absensi['id']) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="id_siswa" class="form-label">Siswa <span class="text-danger">*</span></label>
                <select class="form-select" id="id_siswa" name="id_siswa" required>
                    <option value="" disabled>Pilih Siswa</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s['id'] ?>" 
                                <?= (isset($absensi['id_siswa']) && $absensi['id_siswa'] == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                <select class="form-select" id="id_kelas" name="id_kelas" required>
                    <option value="" disabled>Pilih Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" 
                                <?= (isset($absensi['id_kelas']) && $absensi['id_kelas'] == $k['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                    <option value="" disabled>Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" 
                                <?= (isset($absensi['id_tahun_ajaran']) && $absensi['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_status" class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_status" name="id_status" required>
                    <option value="" disabled>Pilih Status Kehadiran</option>
                    <?php foreach ($statusKehadiran as $st): ?>
                        <option value="<?= $st['id'] ?>" 
                                <?= (isset($absensi['id_status']) && $absensi['id_status'] == $st['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-12">
                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required 
                       value="<?= htmlspecialchars($absensi['tanggal'] ?? '') ?>">
                <div class="form-text">Pilih tanggal kehadiran siswa</div>
            </div>
            
            <div class="col-md-12">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                          placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($absensi['keterangan'] ?? '') ?></textarea>
                <div class="form-text">Contoh: Sakit demam, Izin keperluan keluarga, dll.</div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi/details?id=<?= htmlspecialchars($absensi['id']) ?>" class="btn btn-outline-secondary">
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