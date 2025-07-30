<?php
$pageTitle = "Edit Data Guru";
$currentPage = 'teachers';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$errorMessage = '';
$successMessage = '';
$teacher = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID guru tidak valid atau tidak diberikan.";
} else {
    try {
        $stmt = $pdo->prepare("SELECT * FROM guru WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$teacher) {
            $errorMessage = "Data guru dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $teacher) {
    $nip = $_POST['nip'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $no_telpon = $_POST['no_telpon'] ?? '';

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
            $sql = "UPDATE guru SET nip = :nip, nama = :nama, tanggal_lahir = :tanggal_lahir, jenis_kelamin = :jenis_kelamin, no_telpon = :no_telpon WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nip', $nip);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
            $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
            $stmt->bindParam(':no_telpon', $no_telpon);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header('Location: ' . htmlspecialchars($urlPrefix) . '/teachers');
                exit;
            } else {
                $errorMessage = "Gagal memperbarui data guru. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
    $teacher = array_merge($teacher, [
        'nip' => $nip,
        'nama' => $nama,
        'tanggal_lahir' => $tanggal_lahir,
        'jenis_kelamin' => $jenis_kelamin,
        'no_telpon' => $no_telpon,
    ]);
}

ob_start();
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-2">
    <h1 class="text-2xl font-bold text-primary-700 mb-2 md:mb-0">Edit Data Guru</h1>
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

    <?php if ($teacher): ?>
        <form method="POST" action="" class="space-y-6">
            <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-4">
                    <div>
                        <label for="nip" class="block text-sm font-medium text-primary-700 mb-1">NIP <span
                                    class="text-status-error-500">*</span></label>
                        <input type="text"
                               class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                               id="nip" name="nip" required value="<?= htmlspecialchars($teacher['nip'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="nama" class="block text-sm font-medium text-primary-700 mb-1">Nama <span
                                    class="text-status-error-500">*</span></label>
                        <input type="text"
                               class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                               id="nama" name="nama" required value="<?= htmlspecialchars($teacher['nama'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="no_telpon" class="block text-sm font-medium text-primary-700 mb-1">No. Telepon <span
                                    class="text-status-error-500">*</span></label>
                        <input type="text"
                               class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                               id="no_telpon" name="no_telpon" required
                               value="<?= htmlspecialchars($teacher['no_telpon'] ?? '') ?>">
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-primary-700 mb-1">Tanggal Lahir
                            <span class="text-status-error-500">*</span></label>
                        <input type="date"
                               class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                               id="tanggal_lahir" name="tanggal_lahir" required
                               value="<?= htmlspecialchars($teacher['tanggal_lahir'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-primary-700 mb-1">Jenis Kelamin
                            <span class="text-status-error-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-secondary-300 rounded bg-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                                id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="" disabled>Pilih Jenis Kelamin</option>
                            <option value="1" <?= (isset($teacher['jenis_kelamin']) && $teacher['jenis_kelamin'] == '1') ? 'selected' : '' ?>>
                                Laki-laki
                            </option>
                            <option value="0" <?= (isset($teacher['jenis_kelamin']) && $teacher['jenis_kelamin'] == '0') ? 'selected' : '' ?>>
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
                    Simpan Perubahan
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers/details?id=<?= htmlspecialchars($teacher['id']) ?>"
                   class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-700 rounded hover:bg-secondary-200 transition">
                    <iconify-icon icon="mdi:close" width="20" height="20" class="mr-1"></iconify-icon>
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
