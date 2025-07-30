<?php
$pageTitle = "Tambah Data Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

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
    $errorMessage = "Error loading data: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = $_POST['id_siswa'] ?? '';
    $id_mata_pelajaran = $_POST['id_mata_pelajaran'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_jenis_nilai = $_POST['id_jenis_nilai'] ?? '';
    $nilai = $_POST['nilai'] ?? '';
    $tanggal_penilaian = $_POST['tanggal_penilaian'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if (empty($id_siswa) || empty($id_mata_pelajaran) || empty($id_kelas) || 
        empty($id_tahun_ajaran) || empty($id_jenis_nilai) || empty($nilai) || 
        empty($tanggal_penilaian)) {
        $errorMessage = "Harap lengkapi semua kolom wajib.";
    } elseif (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $errorMessage = "Nilai harus berupa angka antara 0-100.";
    } else {
        try {
            $checkSql = "SELECT COUNT(*) as count FROM nilai 
                        WHERE id_siswa = :id_siswa 
                        AND id_mata_pelajaran = :id_mata_pelajaran 
                        AND id_kelas = :id_kelas 
                        AND id_tahun_ajaran = :id_tahun_ajaran 
                        AND id_jenis_nilai = :id_jenis_nilai";
            
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':id_siswa', $id_siswa);
            $checkStmt->bindParam(':id_mata_pelajaran', $id_mata_pelajaran);
            $checkStmt->bindParam(':id_kelas', $id_kelas);
            $checkStmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
            $checkStmt->bindParam(':id_jenis_nilai', $id_jenis_nilai);
            $checkStmt->execute();
            
            $existingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($existingCount > 0) {
                $errorMessage = "Nilai untuk kombinasi siswa, mata pelajaran, kelas, tahun ajaran, dan jenis nilai ini sudah ada.";
            } else {
                $sql = "INSERT INTO nilai (id_siswa, id_mata_pelajaran, id_kelas, id_tahun_ajaran, id_jenis_nilai, nilai, tanggal_penilaian, keterangan) 
                        VALUES (:id_siswa, :id_mata_pelajaran, :id_kelas, :id_tahun_ajaran, :id_jenis_nilai, :nilai, :tanggal_penilaian, :keterangan)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_siswa', $id_siswa);
                $stmt->bindParam(':id_mata_pelajaran', $id_mata_pelajaran);
                $stmt->bindParam(':id_kelas', $id_kelas);
                $stmt->bindParam(':id_tahun_ajaran', $id_tahun_ajaran);
                $stmt->bindParam(':id_jenis_nilai', $id_jenis_nilai);
                $stmt->bindParam(':nilai', $nilai);
                $stmt->bindParam(':tanggal_penilaian', $tanggal_penilaian);
                $stmt->bindParam(':keterangan', $keterangan);

                if ($stmt->execute()) {
                    header('Location: ' . htmlspecialchars($urlPrefix) . '/nilai');
                    exit;
                } else {
                    $errorMessage = "Gagal menambahkan data nilai. Silakan coba lagi.";
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
    <h1 class="h3 mb-0">Tambah Data Nilai</h1>
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
                <label for="id_mata_pelajaran" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                <select class="form-select" id="id_mata_pelajaran" name="id_mata_pelajaran" required>
                    <option value="" disabled selected>Pilih Mata Pelajaran</option>
                    <?php foreach ($mataPelajaran as $mp): ?>
                        <option value="<?= $mp['id'] ?>" 
                                <?= (isset($_POST['id_mata_pelajaran']) && $_POST['id_mata_pelajaran'] == $mp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mp['nama']) ?>
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
                <label for="id_jenis_nilai" class="form-label">Jenis Nilai <span class="text-danger">*</span></label>
                <select class="form-select" id="id_jenis_nilai" name="id_jenis_nilai" required>
                    <option value="" disabled selected>Pilih Jenis Nilai</option>
                    <?php foreach ($jenisNilai as $jn): ?>
                        <option value="<?= $jn['id'] ?>" 
                                <?= (isset($_POST['id_jenis_nilai']) && $_POST['id_jenis_nilai'] == $jn['id']) ? 'selected' : '' ?>>
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
                       value="<?= htmlspecialchars($_POST['nilai'] ?? '') ?>">
                <div class="form-text">Masukkan nilai antara 0-100</div>
            </div>
            
            <div class="col-md-12">
                <label for="tanggal_penilaian" class="form-label">Tanggal Penilaian <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tanggal_penilaian" name="tanggal_penilaian" required 
                       value="<?= htmlspecialchars($_POST['tanggal_penilaian'] ?? date('Y-m-d')) ?>">
            </div>
            
            <div class="col-md-12">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                          placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
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