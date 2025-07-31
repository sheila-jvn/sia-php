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

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-primary-700">Tambah Data Absensi</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="inline-flex items-center gap-1 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
        <iconify-icon icon="cil:arrow-left" width="20"></iconify-icon>
        Kembali ke Daftar Absensi
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 border border-gray-200">
    <?php if ($errorMessage): ?>
        <div class="flex items-center gap-1 mb-4 bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg">
            <iconify-icon icon="cil:warning" width="22"></iconify-icon>
            <span><?= htmlspecialchars($errorMessage) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_siswa" class="block font-medium text-sm mb-1">Siswa <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_siswa" name="id_siswa" required>
                    <option value="" disabled selected>Pilih Siswa</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (isset($_POST['id_siswa']) && $_POST['id_siswa'] == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_kelas" class="block font-medium text-sm mb-1">Kelas <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_kelas" name="id_kelas" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($_POST['id_kelas']) && $_POST['id_kelas'] == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_tahun_ajaran" class="block font-medium text-sm mb-1">Tahun Ajaran <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                    <option value="" disabled selected>Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= (isset($_POST['id_tahun_ajaran']) && $_POST['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>><?= htmlspecialchars($ta['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_status" class="block font-medium text-sm mb-1">Status Kehadiran <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_status" name="id_status" required>
                    <option value="" disabled selected>Pilih Status Kehadiran</option>
                    <?php foreach ($statusKehadiran as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= (isset($_POST['id_status']) && $_POST['id_status'] == $st['id']) ? 'selected' : '' ?>><?= htmlspecialchars($st['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label for="tanggal" class="block font-medium text-sm mb-1">Tanggal <span class="text-status-error-700">*</span></label>
                <input type="date" class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="tanggal" name="tanggal" required value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>">
                <div class="text-xs text-gray-500 mt-1">Pilih tanggal kehadiran siswa</div>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label for="keterangan" class="block font-medium text-sm mb-1">Keterangan</label>
                <textarea class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                <div class="text-xs text-gray-500 mt-1">Contoh: Sakit demam, Izin keperluan keluarga, dll.</div>
            </div>
        </div>
        <div class="flex flex-wrap justify-end gap-1 pt-2">
            <button type="submit" class="inline-flex items-center gap-1 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                <iconify-icon icon="cil:save" width="20" height="20"></iconify-icon>
                Simpan Data
            </button>
            <button type="reset" class="inline-flex items-center gap-1 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
                <iconify-icon icon="cil:reload" width="20" height="20"></iconify-icon>
                Reset Form
            </button>
        </div>
    </form>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>