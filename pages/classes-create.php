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
    
    $stmtGuru = $pdo->query("SELECT id, nama FROM guru WHERE id NOT IN (SELECT id_guru_wali FROM kelas WHERE id_guru_wali IS NOT NULL) ORDER BY nama");
    $guru = $stmtGuru->fetchAll();
} catch (PDOException $e) {
    $errorMessage = "Error loading data: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'] ?? '';
    $id_tingkat = $_POST['id_tingkat'] ?? '';
    $id_guru_wali = $_POST['id_guru_wali'] ?? '';
    if ($id_guru_wali === '') {
        $id_guru_wali = null;
    }
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

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-primary-700">Tambah Data Kelas</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
        <iconify-icon icon="cil:arrow-left" width="20" height="20"></iconify-icon>
        Kembali ke Daftar Kelas
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 border border-secondary-200">
    <?php if ($errorMessage): ?>
        <div class="flex items-center gap-2 mb-4 bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg">
            <iconify-icon icon="cil:warning" width="22"></iconify-icon>
            <span><?= htmlspecialchars($errorMessage) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2">
                <label for="nama" class="block font-medium text-sm mb-1">Nama Kelas <span class="text-status-error-700">*</span></label>
                <input type="text" class="w-full rounded-lg border border-secondary-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="nama" name="nama" required placeholder="Contoh: XII IPA 1" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            <div>
                <label for="id_tahun_ajaran" class="block font-medium text-sm mb-1">Tahun Ajaran <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-secondary-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                    <option value="" disabled selected>Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= (isset($_POST['id_tahun_ajaran']) && $_POST['id_tahun_ajaran'] == $ta['id']) ? 'selected' : '' ?>><?= htmlspecialchars($ta['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_tingkat" class="block font-medium text-sm mb-1">Tingkat <span class="text-status-error-700">*</span></label>
                <select class="w-full rounded-lg border border-secondary-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_tingkat" name="id_tingkat" required>
                    <option value="" disabled selected>Pilih Tingkat</option>
                    <?php foreach ($tingkat as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (isset($_POST['id_tingkat']) && $_POST['id_tingkat'] == $t['id']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label for="id_guru_wali" class="block font-medium text-sm mb-1">Guru Wali Kelas</label>
                <select class="w-full rounded-lg border border-secondary-300 focus:ring-2 focus:ring-primary-400 px-3 py-2 text-sm" id="id_guru_wali" name="id_guru_wali">
                    <option value="">Pilih Guru Wali (Opsional)</option>
                    <?php foreach ($guru as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= (isset($_POST['id_guru_wali']) && $_POST['id_guru_wali'] == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="text-xs text-gray-500 mt-1">Guru wali kelas dapat diisi kemudian jika belum ditentukan.</div>
            </div>
        </div>
        <div class="flex flex-wrap justify-end gap-2 pt-2">
            <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                <iconify-icon icon="cil:save" width="20" height="20"></iconify-icon>
                Simpan Data
            </button>
            <button type="reset" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
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