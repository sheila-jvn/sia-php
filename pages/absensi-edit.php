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
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-primary-700">Edit Data Absensi</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
        <iconify-icon icon="cil:arrow-left" width="20" height="20"></iconify-icon>
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

    <?php if ($absensi): ?>
    <form method="POST" action="" class="space-y-6">
        <input type="hidden" name="id" value="<?= htmlspecialchars($absensi['id']) ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_siswa" class="block font-medium text-sm mb-1">Siswa <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_siswa" name="id_siswa" required>
                    <option value="" disabled>Pilih Siswa</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (isset($absensi['id_siswa']) && $absensi['id_siswa'] == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_kelas" class="block font-medium text-sm mb-1">Kelas <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_kelas" name="id_kelas" required>
                    <option value="" disabled>Pilih Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($absensi['id_kelas']) && $absensi['id_kelas'] == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_tahun_ajaran" class="block font-medium text-sm mb-1">Tahun Ajaran <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                    <option value="" disabled>Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= (isset($absensi['id_tahun_ajaran']) && $absensi['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>><?= htmlspecialchars($ta['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_status" class="block font-medium text-sm mb-1">Status Kehadiran <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_status" name="id_status" required>
                    <option value="" disabled>Pilih Status Kehadiran</option>
                    <?php foreach ($statusKehadiran as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= (isset($absensi['id_status']) && $absensi['id_status'] == $st['id']) ? 'selected' : '' ?>><?= htmlspecialchars($st['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label for="tanggal" class="block font-medium text-sm mb-1">Tanggal <span class="text-status-error-700">*</span></label>
                <input type="date" class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="tanggal" name="tanggal" required value="<?= htmlspecialchars($absensi['tanggal'] ?? '') ?>">
                <div class="text-xs text-gray-500 mt-1">Pilih tanggal kehadiran siswa</div>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label for="keterangan" class="block font-medium text-sm mb-1">Keterangan</label>
                <textarea class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($absensi['keterangan'] ?? '') ?></textarea>
                <div class="text-xs text-gray-500 mt-1">Contoh: Sakit demam, Izin keperluan keluarga, dll.</div>
            </div>
        </div>
        <div class="flex flex-wrap justify-end gap-1 pt-2">
            <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                <iconify-icon icon="cil:save" width="20" height="20"></iconify-icon>
                Simpan Perubahan
            </button>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi/details?id=<?= htmlspecialchars($absensi['id']) ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                <iconify-icon icon="cil:x" width="20" height="20"></iconify-icon>
                Batal
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