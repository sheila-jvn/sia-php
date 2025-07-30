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

        // Only show teachers not assigned as homeroom teacher, or the current one
        $stmtGuru = $pdo->prepare("SELECT id, nama FROM guru WHERE id NOT IN (SELECT id_guru_wali FROM kelas WHERE id_guru_wali IS NOT NULL AND id != :currentClassId) ORDER BY nama");
        $stmtGuru->bindParam(':currentClassId', $id, PDO::PARAM_INT);
        $stmtGuru->execute();
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
    if ($id_guru_wali === '') {
        $id_guru_wali = null;
    }

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
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-primary-700">Edit Data Kelas</h1>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
           class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
            <iconify-icon icon="cil:arrow-left" width="20"></iconify-icon>
            Kembali ke Daftar Kelas
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
        <?php if ($errorMessage): ?>
            <div class="flex items-center gap-2 mb-4 bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg">
                <iconify-icon icon="cil:warning" width="22"></iconify-icon>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($kelas): ?>
            <form method="POST" action="" class="space-y-6">
                <input type="hidden" name="id" value="<?= htmlspecialchars($kelas['id']) ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label for="nama" class="block font-medium text-sm mb-1">Nama Kelas <span
                                    class="text-status-error-700">*</span></label>
                        <input type="text"
                               class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm"
                               id="nama" name="nama" required placeholder="Contoh: XII IPA 1"
                               value="<?= htmlspecialchars($kelas['nama'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="id_tahun_ajaran" class="block font-medium text-sm mb-1">Tahun Ajaran <span
                                    class="text-status-error-700">*</span></label>
                        <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm"
                                id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                            <option value="" disabled>Pilih Tahun Ajaran</option>
                            <?php foreach ($tahunAjaran as $ta): ?>
                                <option value="<?= $ta['id'] ?>" <?= (isset($kelas['id_tahun_ajaran']) && $kelas['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>><?= htmlspecialchars($ta['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="id_tingkat" class="block font-medium text-sm mb-1">Tingkat <span
                                    class="text-status-error-700">*</span></label>
                        <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm"
                                id="id_tingkat" name="id_tingkat" required>
                            <option value="" disabled>Pilih Tingkat</option>
                            <?php foreach ($tingkat as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($kelas['id_tingkat']) && $kelas['id_tingkat'] == $t['id']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="id_guru_wali" class="block font-medium text-sm mb-1">Guru Wali Kelas</label>
                        <select class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm"
                                id="id_guru_wali" name="id_guru_wali">
                            <option value="">Pilih Guru Wali (Opsional)</option>
                            <?php foreach ($guru as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= (isset($kelas['id_guru_wali']) && $kelas['id_guru_wali'] == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-xs text-gray-500 mt-1">Guru wali kelas dapat dikosongkan jika belum
                            ditentukan.
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap justify-end gap-2 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        <iconify-icon icon="cil:save" width="20"></iconify-icon>
                        Simpan Perubahan
                    </button>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes/details?id=<?= htmlspecialchars($kelas['id']) ?>"
                       class="inline-flex items-center gap-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 border border-secondary-300 px-4 py-2 rounded-lg font-medium transition-colors">
                        <iconify-icon icon="cil:x" width="20"></iconify-icon>
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