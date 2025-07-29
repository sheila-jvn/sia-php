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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hapus Data Guru</h1>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Guru
    </a>
</div>

<div class="card p-4">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($teacher): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['id']) ?>">
            <div class="mb-4">
                <h5>Apakah Anda yakin ingin menghapus data guru berikut?</h5>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item"><strong>Nama:</strong> <?= htmlspecialchars($teacher['nama']) ?></li>
                    <li class="list-group-item"><strong>NIP:</strong> <?= htmlspecialchars($teacher['nip']) ?></li>
                    <li class="list-group-item"><strong>No. Telepon:</strong> <?= htmlspecialchars($teacher['no_telpon']) ?></li>
                    <li class="list-group-item"><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($teacher['tanggal_lahir']) ?></li>
                </ul>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Data yang dihapus tidak dapat dikembalikan!
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger me-2"><i class="bi bi-trash"></i> Hapus</button>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers/details?id=<?= htmlspecialchars($teacher['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i> Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
