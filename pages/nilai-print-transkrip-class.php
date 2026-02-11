<?php
$pageTitle = 'Transkrip Nilai Per Kelas';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$periodStart = $_GET['period_start'] ?? '';
$periodEnd = $_GET['period_end'] ?? '';

$errorMessage = '';
$kelas = null;
$nilaiList = [];
$ganjilId = null;
$genapId = null;
$periodLabel = '';
$totalStudents = 0;

if (!$classId || !$periodStart || !$periodEnd) {
    $errorMessage = 'Parameter laporan belum lengkap.';
}

if (!$errorMessage) {
    if (!ctype_digit((string)$periodStart) || !ctype_digit((string)$periodEnd)) {
        $errorMessage = 'Format periode tidak valid.';
    } else {
        $periodLabel = $periodStart . '/' . $periodEnd;
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT k.id, k.nama
        FROM kelas k
        WHERE k.id = ?');
    $stmt->execute([$classId]);
    $kelas = $stmt->fetch();
    if (!$kelas) {
        $errorMessage = 'Data kelas tidak ditemukan.';
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT id, nama FROM tahun_ajaran WHERE tahun_mulai = :start AND tahun_selesai = :end');
    $stmt->execute([
        ':start' => $periodStart,
        ':end' => $periodEnd,
    ]);
    $years = $stmt->fetchAll();

    foreach ($years as $year) {
        if (stripos($year['nama'], 'ganjil') !== false) {
            $ganjilId = $year['id'];
        } elseif (stripos($year['nama'], 'genap') !== false) {
            $genapId = $year['id'];
        }
    }

    if (!$ganjilId || !$genapId) {
        $errorMessage = 'Data tahun ajaran untuk periode ini belum lengkap.';
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT s.nama AS nama_siswa, s.nis, mp.nama AS mata_pelajaran,
            MAX(CASE WHEN n.id_tahun_ajaran = :ganjil_id AND nj.nama = "UTS" THEN n.nilai END) AS uts_ganjil,
            MAX(CASE WHEN n.id_tahun_ajaran = :ganjil_id AND nj.nama = "UAS" THEN n.nilai END) AS uas_ganjil,
            MAX(CASE WHEN n.id_tahun_ajaran = :genap_id AND nj.nama = "UTS" THEN n.nilai END) AS uts_genap,
            MAX(CASE WHEN n.id_tahun_ajaran = :genap_id AND nj.nama = "UAS" THEN n.nilai END) AS uas_genap
        FROM nilai n
        JOIN siswa s ON n.id_siswa = s.id
        JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
        JOIN nilai_jenis nj ON n.id_jenis_nilai = nj.id
        WHERE n.id_kelas = :class_id
          AND (n.id_tahun_ajaran = :ganjil_id OR n.id_tahun_ajaran = :genap_id)
        GROUP BY s.id, mp.id
        ORDER BY s.nama, mp.nama');
    $stmt->execute([
        ':class_id' => $classId,
        ':ganjil_id' => $ganjilId,
        ':genap_id' => $genapId,
    ]);
    $nilaiList = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT COUNT(DISTINCT n.id_siswa)
        FROM nilai n
        WHERE n.id_kelas = :class_id
          AND (n.id_tahun_ajaran = :ganjil_id OR n.id_tahun_ajaran = :genap_id)');
    $stmt->execute([
        ':class_id' => $classId,
        ':ganjil_id' => $ganjilId,
        ':genap_id' => $genapId,
    ]);
    $totalStudents = (int)$stmt->fetchColumn();
}

function formatScore($value) {
    if ($value === null || $value === '') {
        return '-';
    }
    $formatted = number_format((float)$value, 2, ',', '.');
    return rtrim(rtrim($formatted, '0'), ',');
}

ob_start();
?>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-container { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>

<div class="max-w-7xl mx-auto p-6">
    <div class="print-container bg-white border border-secondary-300 rounded-lg shadow-lg p-8 space-y-6">
        <?php require __DIR__ . '/_kop-surat.php'; ?>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-secondary-900">Transkrip Nilai - Per Kelas</h1>
                <p class="text-sm text-secondary-600">Periode: <?= htmlspecialchars($periodLabel ?: '-') ?></p>
            </div>
            <button onclick="window.print()"
                    class="no-print inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                <iconify-icon icon="solar:printer-linear" width="20" height="20"></iconify-icon>
                Cetak / Simpan PDF
            </button>
        </div>

        <?php if ($errorMessage): ?>
            <div class="rounded-lg border border-status-error-200 bg-status-error-100 text-status-error-700 px-4 py-3">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm font-medium text-secondary-600 mb-2">Kelas</div>
                    <div class="bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 text-secondary-900">
                        <?= htmlspecialchars($kelas['nama'] ?? '-') ?>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-secondary-600 mb-2">Jumlah Siswa</div>
                    <div class="bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 text-secondary-900">
                        <?= htmlspecialchars((string)$totalStudents) ?>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-secondary-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-primary-100 text-primary-700">
                        <tr>
                            <th class="px-4 py-2 font-semibold">No</th>
                            <th class="px-4 py-2 font-semibold text-left">Nama Siswa</th>
                            <th class="px-4 py-2 font-semibold">NIS</th>
                            <th class="px-4 py-2 font-semibold text-left">Mata Pelajaran</th>
                            <th class="px-4 py-2 font-semibold">UTS Ganjil</th>
                            <th class="px-4 py-2 font-semibold">UAS Ganjil</th>
                            <th class="px-4 py-2 font-semibold">UTS Genap</th>
                            <th class="px-4 py-2 font-semibold">UAS Genap</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($nilaiList)): ?>
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-secondary-500 align-middle">
                                    Tidak ada data transkrip untuk periode ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($nilaiList as $index => $nilai): ?>
                                <tr class="even:bg-secondary-50">
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="px-4 py-2 text-secondary-900 align-middle">
                                        <?= htmlspecialchars($nilai['nama_siswa']) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= htmlspecialchars($nilai['nis'] ?? '-') ?>
                                    </td>
                                    <td class="px-4 py-2 text-secondary-900 align-middle">
                                        <?= htmlspecialchars($nilai['mata_pelajaran']) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= htmlspecialchars(formatScore($nilai['uts_ganjil'])) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= htmlspecialchars(formatScore($nilai['uas_ganjil'])) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= htmlspecialchars(formatScore($nilai['uts_genap'])) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= htmlspecialchars(formatScore($nilai['uas_genap'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-right text-xs text-secondary-500">
                Dicetak pada <?= date('d/m/Y H:i') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
?>
