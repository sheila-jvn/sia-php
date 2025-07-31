<?php
$pageTitle = "Hapus Data Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();
$errorMessage = '';
$successMessage = '';
$nilai = null;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $errorMessage = "ID nilai tidak valid atau tidak diberikan.";
} else {
    try {
        $sql = "SELECT 
                    n.id,
                    n.nilai,
                    n.tanggal_penilaian,
                    n.keterangan,
                    s.nama AS nama_siswa,
                    s.nis AS nis_siswa,
                    mp.nama AS nama_mata_pelajaran,
                    k.nama AS nama_kelas,
                    ta.nama AS nama_tahun_ajaran,
                    nj.nama AS jenis_nilai
                FROM nilai n
                JOIN siswa s ON n.id_siswa = s.id
                JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
                JOIN kelas k ON n.id_kelas = k.id
                JOIN tahun_ajaran ta ON n.id_tahun_ajaran = ta.id
                JOIN nilai_jenis nj ON n.id_jenis_nilai = nj.id
                WHERE n.id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $nilai = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nilai) {
            $errorMessage = "Data nilai dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nilai) {
    try {
        $stmt = $pdo->prepare("DELETE FROM nilai WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: ' . htmlspecialchars($urlPrefix) . '/nilai?deleted=1');
            exit;
        } else {
            $errorMessage = "Gagal menghapus data nilai. Silakan coba lagi.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

ob_start();
?>
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-primary-700">Hapus Data Nilai</h1>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="mdi:arrow-left" width="20" height="20"></iconify-icon>
            Kembali ke Daftar Nilai
        </a>    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <?php if ($errorMessage): ?>
            <div class="flex items-center gap-3 mb-6 p-4 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200">
                <iconify-icon icon="mdi:alert-circle" width="22" class="shrink-0"></iconify-icon>
                <div class="flex-1"> <?= htmlspecialchars($errorMessage) ?> </div>
            </div>
            <?php if ($nilai): ?>
                <div class="flex justify-end mt-3">
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                        <iconify-icon icon="mdi:arrow-left" width="20" height="20"></iconify-icon>
                        Kembali ke Daftar Nilai
                    </a>
                </div>
            <?php endif; ?>
        <?php elseif ($nilai): ?>
            <form method="POST" action="" class="space-y-6">
                <input type="hidden" name="id" value="<?= htmlspecialchars($nilai['id']) ?>">
                <div class="mb-4">
                    <h5 class="text-lg font-semibold">Apakah Anda yakin ingin menghapus data nilai berikut?</h5>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 mt-3 p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p>
                                    <span class="font-semibold">Siswa:</span> <?= htmlspecialchars($nilai['nama_siswa']) ?>
                                </p>
                                <p><span class="font-semibold">NIS:</span> <?= htmlspecialchars($nilai['nis_siswa']) ?>
                                </p>
                                <p>
                                    <span class="font-semibold">Mata Pelajaran:</span> <?= htmlspecialchars($nilai['nama_mata_pelajaran']) ?>
                                </p>
                                <p>
                                    <span class="font-semibold">Kelas:</span> <?= htmlspecialchars($nilai['nama_kelas']) ?>
                                </p>
                            </div>
                            <div>
                                <p>
                                    <span class="font-semibold">Tahun Ajaran:</span> <?= htmlspecialchars($nilai['nama_tahun_ajaran']) ?>
                                </p>
                                <p><span class="font-semibold">Jenis Nilai:</span> <span
                                            class="inline-block rounded px-2 py-1 text-xs font-semibold bg-accent-100 text-accent-700"> <?= htmlspecialchars($nilai['jenis_nilai']) ?> </span>
                                </p>
                                <p><span class="font-semibold">Nilai:</span> <?php $nilaiNum = (float)$nilai['nilai'];
                                    $nilaiColor = $nilaiNum >= 80 ? 'text-status-success-700' : ($nilaiNum >= 70 ? 'text-status-warning-700' : 'text-status-error-700'); ?>
                                    <span class="font-bold text-lg <?= $nilaiColor ?>"> <?= htmlspecialchars($nilai['nilai']) ?> </span>
                                </p>
                                <p>
                                    <span class="font-semibold">Tanggal Penilaian:</span> <?= date('d/m/Y', strtotime($nilai['tanggal_penilaian'])) ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($nilai['keterangan']): ?>
                            <div class="mt-4">
                                <p>
                                    <span class="font-semibold">Keterangan:</span> <?= htmlspecialchars($nilai['keterangan']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-3 mt-4 p-4 rounded-lg bg-status-warning-100 text-status-warning-700 border border-status-warning-200">
                        <iconify-icon icon="mdi:alert" width="22" class="shrink-0"></iconify-icon>
                        <div>
                            <span class="font-semibold">Peringatan!</span><br>
                            Data nilai yang dihapus tidak dapat dikembalikan. Pastikan Anda benar-benar ingin menghapus
                            data ini.
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-end gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition">
                        <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
                        Ya, Hapus Nilai
                    </button>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
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