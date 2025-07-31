<?php
$pageTitle = "Detail Data Nilai";
$currentPage = 'nilai'; 

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$nilai = null;
$errorMessage = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

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
} else {
    $errorMessage = "ID nilai tidak valid atau tidak diberikan.";
}

ob_start();
?>

<div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
    <h1 class="text-2xl font-bold text-primary-700">Detail Data Nilai</h1>
    <div class="flex flex-row gap-2">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="mdi:arrow-left" width="20" height="20"></iconify-icon>
            Kembali ke Daftar Nilai
        </a>
        <?php if ($nilai): ?>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai/edit?id=<?= htmlspecialchars($nilai['id']) ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition">
                <iconify-icon icon="mdi:pencil-outline" width="20" height="20"></iconify-icon>
                Edit Data
            </a>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai/delete?id=<?= htmlspecialchars($nilai['id']) ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-error-500 text-white hover:bg-status-error-600 transition">
                <iconify-icon icon="mdi:trash-can-outline" width="20" height="20"></iconify-icon>
                Hapus Data
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($errorMessage): ?>
    <div class="flex items-center gap-3 mb-6 p-4 rounded-lg bg-status-error-100 text-status-error-700 border border-status-error-200">
        <iconify-icon icon="mdi:alert-circle" width="22" class="shrink-0"></iconify-icon>
        <div class="flex-1"> <?= htmlspecialchars($errorMessage) ?> </div>
    </div>
<?php elseif ($nilai): ?>
    <div class="bg-white rounded-xl shadow">
        <div class="border-b px-6 py-4">
            <h5 class="text-lg font-semibold flex items-center gap-2 mb-0">
                <iconify-icon icon="mdi:clipboard-text-outline" width="22"></iconify-icon>
                Informasi Nilai
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">ID Nilai</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700"> <?= htmlspecialchars($nilai['id']) ?> </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Nama Siswa</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700">
                            <?= htmlspecialchars($nilai['nama_siswa']) ?>
                            <?php if ($nilai['nis_siswa']): ?>
                                <span class="block text-xs text-gray-500 mt-1">NIS: <?= htmlspecialchars($nilai['nis_siswa']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Mata Pelajaran</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700"> <?= htmlspecialchars($nilai['nama_mata_pelajaran']) ?> </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Kelas</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700"> <?= htmlspecialchars($nilai['nama_kelas']) ?> </div>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Tahun Ajaran</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700"> <?= htmlspecialchars($nilai['nama_tahun_ajaran']) ?> </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Jenis Nilai</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                            <span class="inline-block rounded px-2 py-1 text-xs font-semibold bg-accent-100 text-accent-700"> <?= htmlspecialchars($nilai['jenis_nilai']) ?> </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Nilai</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                            <?php 
                                $nilaiNum = (float)$nilai['nilai'];
                                $nilaiColor = $nilaiNum >= 80 ? 'text-status-success-700' : ($nilaiNum >= 70 ? 'text-status-warning-700' : 'text-status-error-700');
                            ?>
                            <span class="font-bold text-2xl <?= $nilaiColor ?>"> <?= htmlspecialchars($nilai['nilai']) ?> </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-primary-700 mb-1">Tanggal Penilaian</label>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700"> <?= date('d/m/Y', strtotime($nilai['tanggal_penilaian'])) ?> </div>
                    </div>
                </div>
            </div>
            <?php if ($nilai['keterangan']): ?>
                <div class="mt-6">
                    <label class="block font-semibold text-primary-700 mb-1">Keterangan</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 text-gray-700 min-h-[80px]">
                        <?= nl2br(htmlspecialchars($nilai['keterangan'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>