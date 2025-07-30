<?php
$pageTitle = "Tambah Data Siswa";
$currentPage = 'students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $nisn = $_POST['nisn'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $no_kk = $_POST['no_kk'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $nama_ayah = $_POST['nama_ayah'] ?? '';
    $nama_ibu = $_POST['nama_ibu'] ?? '';
    $nik_ayah = $_POST['nik_ayah'] ?? '';
    $nik_ibu = $_POST['nik_ibu'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    // Basic validation
    if (empty($nis) || empty($nisn) || empty($nama) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($alamat)) {
        $errorMessage = "Harap lengkapi semua kolom wajib (NIS, NISN, Nama, Tanggal Lahir, Jenis Kelamin, Alamat).";
    } else {
        try {
            $sql = "INSERT INTO siswa (nis, nisn, nama, no_kk, tanggal_lahir, jenis_kelamin, nama_ayah, nama_ibu, nik_ayah, nik_ibu, alamat)
                    VALUES (:nis, :nisn, :nama, :no_kk, :tanggal_lahir, :jenis_kelamin, :nama_ayah, :nama_ibu, :nik_ayah, :nik_ibu, :alamat)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nis', $nis);
            $stmt->bindParam(':nisn', $nisn);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':no_kk', $no_kk);
            $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
            $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
            $stmt->bindParam(':nama_ayah', $nama_ayah);
            $stmt->bindParam(':nama_ibu', $nama_ibu);
            $stmt->bindParam(':nik_ayah', $nik_ayah);
            $stmt->bindParam(':nik_ibu', $nik_ibu);
            $stmt->bindParam(':alamat', $alamat);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/students');
                exit;
            } else {
                $errorMessage = "Gagal menambahkan data siswa. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-primary-700">Tambah Data Siswa</h1>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/students"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="cil:arrow-left"></iconify-icon>
            Kembali ke Daftar Siswa
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <?php if ($errorMessage): ?>
            <div class="flex items-center gap-2 mb-4 p-4 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200">
                <iconify-icon icon="cil:warning"></iconify-icon>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nis" class="block font-medium text-sm mb-1">NIS <span
                                class="text-status-error-600">*</span></label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nis" name="nis" required value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">
                </div>
                <div>
                    <label for="nisn" class="block font-medium text-sm mb-1">NISN <span
                                class="text-status-error-600">*</span></label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nisn" name="nisn" required value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>">
                </div>
                <div class="md:col-span-2">
                    <label for="nama" class="block font-medium text-sm mb-1">Nama <span
                                class="text-status-error-600">*</span></label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>
                <div>
                    <label for="no_kk" class="block font-medium text-sm mb-1">Nomor Kartu Keluarga</label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="no_kk" name="no_kk" value="<?= htmlspecialchars($_POST['no_kk'] ?? '') ?>">
                </div>
                <div>
                    <label for="tanggal_lahir" class="block font-medium text-sm mb-1">Tanggal Lahir <span
                                class="text-status-error-600">*</span></label>
                    <input type="date"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="tanggal_lahir" name="tanggal_lahir" required
                           value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block font-medium text-sm mb-1">Jenis Kelamin <span
                                class="text-status-error-600">*</span></label>
                    <select class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                            id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                        <option value="1" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '1') ? 'selected' : '' ?>>
                            Laki-laki
                        </option>
                        <option value="0" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '0') ? 'selected' : '' ?>>
                            Perempuan
                        </option>
                    </select>
                </div>
                <div>
                    <label for="alamat" class="block font-medium text-sm mb-1">Alamat <span
                                class="text-status-error-600">*</span></label>
                    <textarea
                            class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                            id="alamat" name="alamat" rows="3"
                            required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="nama_ayah" class="block font-medium text-sm mb-1">Nama Ayah</label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nama_ayah" name="nama_ayah" value="<?= htmlspecialchars($_POST['nama_ayah'] ?? '') ?>">
                </div>
                <div>
                    <label for="nik_ayah" class="block font-medium text-sm mb-1">NIK Ayah</label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nik_ayah" name="nik_ayah" value="<?= htmlspecialchars($_POST['nik_ayah'] ?? '') ?>">
                </div>
                <div>
                    <label for="nama_ibu" class="block font-medium text-sm mb-1">Nama Ibu</label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nama_ibu" name="nama_ibu" value="<?= htmlspecialchars($_POST['nama_ibu'] ?? '') ?>">
                </div>
                <div>
                    <label for="nik_ibu" class="block font-medium text-sm mb-1">NIK Ibu</label>
                    <input type="text"
                           class="w-full rounded-lg border border-secondary-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white text-sm"
                           id="nik_ibu" name="nik_ibu" value="<?= htmlspecialchars($_POST['nik_ibu'] ?? '') ?>">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                    <iconify-icon icon="cil:save"></iconify-icon>
                    Simpan Data
                </button>
                <button type="reset"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                    <iconify-icon icon="cil:refresh"></iconify-icon>
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