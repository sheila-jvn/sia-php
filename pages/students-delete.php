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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hapus Data Siswa</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($student): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
            <div class="mb-4">
                <h5>Apakah Anda yakin ingin menghapus data siswa berikut?</h5>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item"><strong>Nama:</strong> <?= htmlspecialchars($student['nama']) ?></li>
                    <li class="list-group-item"><strong>NIS:</strong> <?= htmlspecialchars($student['nis']) ?></li>
                    <li class="list-group-item"><strong>NISN:</strong> <?= htmlspecialchars($student['nisn']) ?></li>
                    <li class="list-group-item"><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($student['tanggal_lahir']) ?></li>
                </ul>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Data yang dihapus tidak dapat dikembalikan!
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger me-2"><i class="bi bi-trash"></i> Hapus</button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students/details?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i> Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
