<?php
$pageTitle = 'Laporan UAS';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$tahunAjaranId = isset($_GET['tahun_ajaran_id']) ? (int)$_GET['tahun_ajaran_id'] : 0;

$errorMessage = '';
$student = null;
$tahunAjaran = null;
$nilaiList = [];
$classNames = [];
$jenisId = null;

if (!$studentId || !$tahunAjaranId) {
    $errorMessage = 'Parameter laporan belum lengkap.';
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT id FROM nilai_jenis WHERE nama = ?');
    $stmt->execute(['UAS']);
    $jenisId = $stmt->fetchColumn();
    if (!$jenisId) {
        $errorMessage = 'Jenis nilai UAS tidak ditemukan.';
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT id, nama, nis, nisn FROM siswa WHERE id = ?');
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    if (!$student) {
        $errorMessage = 'Data siswa tidak ditemukan.';
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT id, nama, tahun_mulai, tahun_selesai FROM tahun_ajaran WHERE id = ?');
    $stmt->execute([$tahunAjaranId]);
    $tahunAjaran = $stmt->fetch();
    if (!$tahunAjaran) {
        $errorMessage = 'Data tahun ajaran tidak ditemukan.';
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT mp.nama AS mata_pelajaran, n.nilai, n.tanggal_penilaian, n.keterangan
        FROM nilai n
        JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
        WHERE n.id_siswa = :student_id
          AND n.id_tahun_ajaran = :tahun_ajaran_id
          AND n.id_jenis_nilai = :jenis_id
        ORDER BY mp.nama');
    $stmt->execute([
        ':student_id' => $studentId,
        ':tahun_ajaran_id' => $tahunAjaranId,
        ':jenis_id' => $jenisId,
    ]);
    $nilaiList = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT DISTINCT k.nama
        FROM nilai n
        JOIN kelas k ON n.id_kelas = k.id
        WHERE n.id_siswa = :student_id
          AND n.id_tahun_ajaran = :tahun_ajaran_id
          AND n.id_jenis_nilai = :jenis_id
        ORDER BY k.nama');
    $stmt->execute([
        ':student_id' => $studentId,
        ':tahun_ajaran_id' => $tahunAjaranId,
        ':jenis_id' => $jenisId,
    ]);
    $classNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
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

<div class="max-w-5xl mx-auto p-6">
    <div class="print-container bg-white border border-secondary-300 rounded-lg shadow-lg p-8 space-y-6">
        <?php require __DIR__ . '/_kop-surat.php'; ?>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-secondary-900">Laporan Ulangan Akhir Semester (UAS)</h1>
                <p class="text-sm text-secondary-600">Tahun Ajaran: <?= htmlspecialchars($tahunAjaran['nama'] ?? '-') ?></p>
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
                    <div class="text-sm font-medium text-secondary-600 mb-2">Nama Siswa</div>
                    <div class="bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 text-secondary-900">
                        <?= htmlspecialchars($student['nama'] ?? '-') ?>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-secondary-600 mb-2">NIS</div>
                    <div class="bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 text-secondary-900">
                        <?= htmlspecialchars($student['nis'] ?? '-') ?>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-secondary-600 mb-2">NISN</div>
                    <div class="bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 text-secondary-900">
                        <?= htmlspecialchars($student['nisn'] ?? '-') ?>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-secondary-600 mb-2">Kelas</div>
                    <div class="bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 text-secondary-900">
                        <?= htmlspecialchars($classNames ? implode(', ', $classNames) : '-') ?>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-secondary-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-primary-100 text-primary-700">
                        <tr>
                            <th class="px-4 py-2 font-semibold">No</th>
                            <th class="px-4 py-2 font-semibold text-left">Mata Pelajaran</th>
                            <th class="px-4 py-2 font-semibold">Nilai</th>
                            <th class="px-4 py-2 font-semibold">Tanggal</th>
                            <th class="px-4 py-2 font-semibold text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($nilaiList)): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-secondary-500 align-middle">
                                    Tidak ada data nilai UAS untuk filter ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($nilaiList as $index => $nilai): ?>
                                <tr class="even:bg-secondary-50">
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="px-4 py-2 text-secondary-900 align-middle">
                                        <?= htmlspecialchars($nilai['mata_pelajaran']) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center font-semibold text-secondary-900 align-middle">
                                        <?= htmlspecialchars(formatScore($nilai['nilai'])) ?>
                                    </td>
                                    <td class="px-4 py-2 text-center text-secondary-900 align-middle">
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($nilai['tanggal_penilaian']))) ?>
                                    </td>
                                    <td class="px-4 py-2 text-secondary-900 align-middle">
                                        <?= htmlspecialchars($nilai['keterangan'] ?: '-') ?>
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
