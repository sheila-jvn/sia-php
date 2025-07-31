<?php
$pageTitle = "Hapus Data Guru";
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
    try {
        $stmt = $pdo->prepare("DELETE FROM guru WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: ' . htmlspecialchars($urlPrefix) . '/teachers');
            exit;
        } else {
            $errorMessage = "Gagal menghapus data guru. Silakan coba lagi.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

ob_start();
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-2">
    <h1 class="text-2xl font-bold text-primary-700 mb-2 md:mb-0">Hapus Data Guru</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers"
       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
        <iconify-icon icon="mdi:arrow-left" width="20" height="20"></iconify-icon>
        Kembali ke Daftar Guru
    </a>
</div>

<div class="bg-white rounded shadow p-6">
    <?php if ($errorMessage): ?>
        <div class="mb-4 px-4 py-3 rounded bg-status-error-100 text-status-error-700 border border-status-error-200">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php elseif ($teacher): ?>
        <form method="POST" action="" class="space-y-6">
            <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Apakah Anda yakin ingin menghapus data guru berikut?</h2>
                <ul class="mb-3 space-y-1">
                    <li>
                        <span class="font-semibold text-primary-700">Nama:</span> <?= htmlspecialchars($teacher['nama']) ?>
                    </li>
                    <li>
                        <span class="font-semibold text-primary-700">NIP:</span> <?= htmlspecialchars($teacher['nip']) ?>
                    </li>
                    <li>
                        <span class="font-semibold text-primary-700">No. Telepon:</span> <?= htmlspecialchars($teacher['no_telpon']) ?>
                    </li>
                    <li>
                        <span class="font-semibold text-primary-700">Tanggal Lahir:</span> <?= htmlspecialchars($teacher['tanggal_lahir']) ?>
                    </li>
                </ul>
                <div class="flex items-center gap-2 px-4 py-3 rounded bg-status-warning-100 text-status-warning-700 border border-status-warning-200">
                    <iconify-icon icon="mdi:alert" width="20" height="20"></iconify-icon>
                    Data yang dihapus tidak dapat dikembalikan!
                </div>
            </div>
            <div class="flex flex-row justify-end gap-2 mt-6">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition">
                    <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
                    Hapus
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers/details?id=<?= htmlspecialchars($teacher['id']) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                    <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
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
