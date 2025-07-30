<?php
$pageTitle = "Hapus Data Siswa";
$currentPage = 'students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();
$errorMessage = '';
$successMessage = '';
$student = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID siswa tidak valid atau tidak diberikan.";
} else {
    // Fetch student data for confirmation
    try {
        $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) {
            $errorMessage = "Data siswa dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $student) {
    try {
        $stmt = $pdo->prepare("DELETE FROM siswa WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: ' . htmlspecialchars($urlPrefix) . '/students');
            exit;
        } else {
            $errorMessage = "Gagal menghapus data siswa. Silakan coba lagi.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

ob_start();
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-primary-700">Hapus Data Siswa</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/students"
       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
        <iconify-icon icon="cil:arrow-left"></iconify-icon>
        Kembali ke Daftar Siswa
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <?php if ($errorMessage): ?>
        <div class="flex items-center gap-2 p-4 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200">
            <iconify-icon icon="cil:warning"></iconify-icon>
            <span><?= htmlspecialchars($errorMessage) ?></span>
        </div>
    <?php elseif ($student): ?>
        <form method="POST" action="" class="space-y-6">
            <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Apakah Anda yakin ingin menghapus data siswa
                    berikut?</h2>
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center p-3 rounded-lg bg-secondary-50 border border-secondary-200">
                        <span class="font-semibold text-sm">Nama:</span>
                        <span><?= htmlspecialchars($student['nama']) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-lg bg-secondary-50 border border-secondary-200">
                        <span class="font-semibold text-sm">NIS:</span>
                        <span><?= htmlspecialchars($student['nis']) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-lg bg-secondary-50 border border-secondary-200">
                        <span class="font-semibold text-sm">NISN:</span>
                        <span><?= htmlspecialchars($student['nisn']) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-lg bg-secondary-50 border border-secondary-200">
                        <span class="font-semibold text-sm">Tanggal Lahir:</span>
                        <span><?= htmlspecialchars($student['tanggal_lahir']) ?></span>
                    </div>
                </div>
                <div class="flex items-center gap-2 p-4 rounded-lg bg-status-warning-100 text-status-warning-700 border border-status-warning-200">
                    <iconify-icon icon="cil:warning" class="text-lg"></iconify-icon>
                    <span>Data yang dihapus tidak dapat dikembalikan!</span>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition">
                    <iconify-icon icon="cil:trash"></iconify-icon>
                    Hapus
                </button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students/details?id=<?= htmlspecialchars($student['id']) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                    <iconify-icon icon="cil:x"></iconify-icon>
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
