<?php
$pageTitle = "Edit Data Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';
$successMessage = '';
$nilai = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID nilai tidak valid atau tidak diberikan.";
} else {
    try {
        $sql = "SELECT 
                    n.id,
                    n.id_siswa,
                    n.id_mata_pelajaran,
                    n.id_kelas,
                    n.id_tahun_ajaran,
                    n.id_jenis_nilai,
                    n.nilai,
                    n.tanggal_penilaian,
                    n.keterangan,
                    s.nama AS nama_siswa,
                    mp.nama AS nama_mata_pelajaran,
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    nj.nama AS jenis_nilai
                FROM nilai n
                JOIN siswa s ON n.id_siswa = s.id
                JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
                JOIN kelas k ON n.id_kelas = k.id
                JOIN tahun_ajaran ta ON n.id_tahun_ajaran = ta.id
                JOIN nilai_jenis nj ON n.id_jenis_nilai = nj.id
                WHERE n.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $nilai = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$nilai) {
            $errorMessage = "Data nilai dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

$siswa = [];
$mataPelajaran = [];
$kelas = [];
$tahunAjaran = [];
$jenisNilai = [];

if (!$errorMessage) {
    try {
        $stmtSiswa = $pdo->query("SELECT id, nama FROM siswa ORDER BY nama");
        $siswa = $stmtSiswa->fetchAll();
        
        $stmtMapel = $pdo->query("SELECT id, nama FROM mata_pelajaran ORDER BY nama");
        $mataPelajaran = $stmtMapel->fetchAll();
        
        $stmtKelas = $pdo->query("SELECT id, nama FROM kelas ORDER BY nama");
        $kelas = $stmtKelas->fetchAll();
        
        $stmtTahun = $pdo->query("SELECT id, nama FROM tahun_ajaran ORDER BY nama");
        $tahunAjaran = $stmtTahun->fetchAll();
        
        $stmtJenis = $pdo->query("SELECT id, nama FROM nilai_jenis ORDER BY nama");
        $jenisNilai = $stmtJenis->fetchAll();
        
    } catch (PDOException $e) {
        $errorMessage = "Error loading dropdown data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nilai) {
    $id_siswa = $_POST['id_siswa'] ?? '';
    $id_mata_pelajaran = $_POST['id_mata_pelajaran'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_jenis_nilai = $_POST['id_jenis_nilai'] ?? '';
    $nilai_input = $_POST['nilai'] ?? '';
    $tanggal_penilaian = $_POST['tanggal_penilaian'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if (empty($id_siswa) || empty($id_mata_pelajaran) || empty($id_kelas) || 
        empty($id_tahun_ajaran) || empty($id_jenis_nilai) || empty($nilai_input) || 
        empty($tanggal_penilaian)) {
        $errorMessage = "Harap lengkapi semua kolom wajib.";
    } elseif (!is_numeric($nilai_input) || $nilai_input < 0 || $nilai_input > 100) {
        $errorMessage = "Nilai harus berupa angka antara 0-100.";
    } else {
        try {
            $checkSql = "SELECT COUNT(*) as count FROM nilai 
                        WHERE id_siswa = :id_siswa 
                        AND id_mata_pelajaran = :id_mata_pelajaran 
                        AND id_kelas = :id_kelas 
                        AND id_tahun_ajaran = :id_tahun_ajaran 
                        AND id_jenis_nilai = :id_jenis_nilai
                        AND id != :current_id";
            
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':id_siswa', $id_siswa);
            $checkStmt->bindParam(':id_mata_pelajaran', $id_mata_pelajaran);
            $checkStmt->bindParam(':id_kelas', $id_kelas);
            $checkStmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $checkStmt->bindParam(':id_jenis_nilai', $id_jenis_nilai);
            $checkStmt->bindParam(':current_id', $id);
            $checkStmt->execute();
            
            $existingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($existingCount > 0) {
                $errorMessage = "Nilai untuk kombinasi siswa, mata pelajaran, kelas, tahun ajaran, dan jenis nilai ini sudah ada.";
            } else {
                $sql = "UPDATE nilai SET 
                            id_siswa = :id_siswa,
                            id_mata_pelajaran = :id_mata_pelajaran,
                            id_kelas = :id_kelas,
                            id_tahun_ajaran = :id_tahun_ajaran,
                            id_jenis_nilai = :id_jenis_nilai,
                            nilai = :nilai,
                            tanggal_penilaian = :tanggal_penilaian,
                            keterangan = :keterangan
                        WHERE id = :id";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_siswa', $id_siswa);
                $stmt->bindParam(':id_mata_pelajaran', $id_mata_pelajaran);
                $stmt->bindParam(':id_kelas', $id_kelas);
                $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
                $stmt->bindParam(':id_jenis_nilai', $id_jenis_nilai);
                $stmt->bindParam(':nilai', $nilai_input);
                $stmt->bindParam(':tanggal_penilaian', $tanggal_penilaian);
                $stmt->bindParam(':keterangan', $keterangan);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    header('Location: ' . htmlspecialchars($urlPrefix) . '/nilai/details?id=' . $id);
                    exit;
                } else {
                    $errorMessage = "Gagal memperbarui data nilai. Silakan coba lagi.";
                }
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
    
    $nilai = array_merge($nilai, [
        'id_siswa' => $id_siswa,
        'id_mata_pelajaran' => $id_mata_pelajaran,
        'id_kelas' => $id_kelas,
        'id_tahun_ajaran' => $id_tahun_ajaran,
        'id_jenis_nilai' => $id_jenis_nilai,
        'nilai' => $nilai_input,
        'tanggal_penilaian' => $tanggal_penilaian,
        'keterangan' => $keterangan,
    ]);
}

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Data Nilai</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Nilai
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($nilai): ?>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($nilai['id']) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="id_siswa" class="form-label">Siswa <span class="text-danger">*</span></label>
                <select class="form-select" id="id_siswa" name="id_siswa" required>
                    <option value="" disabled>Pilih Siswa</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s['id'] ?>" 
                                <?= (isset($nilai['id_siswa']) && $nilai['id_siswa'] == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_mata_pelajaran" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_mata_pelajaran" name="id_mata_pelajaran" required>
                    <option value="" disabled>Pilih Mata Pelajaran</option>
                    <?php foreach ($mataPelajaran as $mp): ?>
                        <option value="<?= $mp['id'] ?>" 
                                <?= (isset($nilai['id_mata_pelajaran']) && $nilai['id_mata_pelajaran'] == $mp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mp['nama']) ?>
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
                                <?= (isset($nilai['id_kelas']) && $nilai['id_kelas'] == $k['id']) ? 'selected' : '' ?>>
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
                                <?= (isset($nilai['id_tahun_ajaran']) && $nilai['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="id_jenis_nilai" class="form-label">Jenis Nilai <span class="text-danger">*</span></label>
                <select class="form-select" id="id_jenis_nilai" name="id_jenis_nilai" required>
                    <option value="" disabled>Pilih Jenis Nilai</option>
                    <?php foreach ($jenisNilai as $jn): ?>
                        <option value="<?= $jn['id'] ?>" 
                                <?= (isset($nilai['id_jenis_nilai']) && $nilai['id_jenis_nilai'] == $jn['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($jn['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="nilai" class="form-label">Nilai <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="nilai" name="nilai" required 
                       min="0" max="100" step="0.01"
                       placeholder="0-100" 
                       value="<?= htmlspecialchars($nilai['nilai'] ?? '') ?>">
                <div class="form-text">Masukkan nilai antara 0-100</div>
            </div>
            
            <div class="col-md-12">
                <label for="tanggal_penilaian" class="form-label">Tanggal Penilaian <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tanggal_penilaian" name="tanggal_penilaian" required 
                       value="<?= htmlspecialchars($nilai['tanggal_penilaian'] ?? '') ?>">
            </div>
            
            <div class="col-md-12">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                          placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($nilai['keterangan'] ?? '') ?></textarea>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai/details?id=<?= htmlspecialchars($nilai['id']) ?>" class="btn btn-outline-secondary">
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