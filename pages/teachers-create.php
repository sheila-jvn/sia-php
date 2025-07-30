<?php
$pageTitle = "Tambah Data Guru";
$currentPage = 'teachers';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = $_POST['nip'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $no_telpon = $_POST['no_telpon'] ?? '';

    // Basic validation
    if (
        $nip === '' ||
        $nama === '' ||
        $tanggal_lahir === '' ||
        $jenis_kelamin === '' ||
        $no_telpon === ''
    ) {
        $errorMessage = "Harap lengkapi semua kolom wajib (NIP, Nama, Tanggal Lahir, Jenis Kelamin, No. Telepon).";
    } else {
        try {
            $sql = "INSERT INTO guru (nip, nama, tanggal_lahir, jenis_kelamin, no_telpon)
                    VALUES (:nip, :nama, :tanggal_lahir, :jenis_kelamin, :no_telpon)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nip', $nip);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
            $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
            $stmt->bindParam(':no_telpon', $no_telpon);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/teachers');
                exit;
            } else {
                $errorMessage = "Gagal menambahkan data guru. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

ob_start();
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-2">
    <h1 class="text-2xl font-bold text-primary-700 mb-2 md:mb-0">Tambah Data Guru</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers"
       class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-700 hover:bg-secondary-200 rounded">
        <iconify-icon icon="mdi:arrow-left" width="20" height="20" class="mr-1"></iconify-icon>
        Kembali ke Daftar Guru
    </a>
</div>

<div class="bg-white rounded shadow p-6">
    <?php if ($errorMessage): ?>
        <div class="mb-4 px-4 py-3 rounded bg-status-error-100 text-status-error-700 border border-status-error-200">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-4">
                <div>
                    <label for="nip" class="block text-sm font-medium text-primary-700 mb-1">NIP <span
                                class="text-status-error-500">*</span></label>
                    <input type="text"
                           class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                           id="nip" name="nip" required value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>">
                </div>
                <div>
                    <label for="nama" class="block text-sm font-medium text-primary-700 mb-1">Nama <span
                                class="text-status-error-500">*</span></label>
                    <input type="text"
                           class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                           id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>
                <div>
                    <label for="no_telpon" class="block text-sm font-medium text-primary-700 mb-1">No. Telepon <span
                                class="text-status-error-500">*</span></label>
                    <input type="text"
                           class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                           id="no_telpon" name="no_telpon" required
                           value="<?= htmlspecialchars($_POST['no_telpon'] ?? '') ?>">
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-primary-700 mb-1">Tanggal Lahir
                        <span class="text-status-error-500">*</span></label>
                    <input type="date"
                           class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                           id="tanggal_lahir" name="tanggal_lahir" required
                           value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-primary-700 mb-1">Jenis Kelamin
                        <span class="text-status-error-500">*</span></label>
                    <select class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                            id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="" disabled <?= !isset($_POST['jenis_kelamin']) ? 'selected' : '' ?>>Pilih Jenis
                            Kelamin
                        </option>
                        <option value="1" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '1') ? 'selected' : '' ?>>
                            Laki-laki
                        </option>
                        <option value="0" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == '0') ? 'selected' : '' ?>>
                            Perempuan
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex flex-row justify-end gap-2 mt-6">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">
                <iconify-icon icon="mdi:content-save-outline" width="20" height="20" class="mr-1"></iconify-icon>
                Simpan Data
            </button>
            <button type="reset"
                    class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition">
                <iconify-icon icon="mdi:arrow-u-left-top" width="20" height="20" class="mr-1"></iconify-icon>
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
