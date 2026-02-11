<?php
$pageTitle = "Laporan Nilai";
$currentPage = 'nilai';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$reportType = $_GET['report'] ?? 'uts';
$scope = $_GET['scope'] ?? 'student';
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$tahunAjaranId = isset($_GET['tahun_ajaran_id']) ? (int)$_GET['tahun_ajaran_id'] : 0;
$period = $_GET['period'] ?? '';

$errors = [];
$reportUrl = '';

$students = [];
$classes = [];
$tahunAjaran = [];
$periods = [];

try {
    $students = $pdo->query('SELECT id, nama, nis FROM siswa ORDER BY nama')->fetchAll();
    $classes = $pdo->query('SELECT id, nama FROM kelas ORDER BY nama')->fetchAll();
    $tahunAjaran = $pdo->query('SELECT id, nama, tahun_mulai, tahun_selesai FROM tahun_ajaran ORDER BY tahun_mulai DESC, nama DESC')->fetchAll();
    $periods = $pdo->query('SELECT tahun_mulai, tahun_selesai FROM tahun_ajaran GROUP BY tahun_mulai, tahun_selesai ORDER BY tahun_mulai DESC')->fetchAll();
} catch (PDOException $e) {
    $errors[] = 'Gagal memuat data filter laporan.';
}

$validReportTypes = ['uts', 'uas', 'transkrip'];
$validScopes = ['student', 'class'];

if (!in_array($reportType, $validReportTypes, true)) {
    $reportType = 'uts';
}

if (!in_array($scope, $validScopes, true)) {
    $scope = 'student';
}

$isTranskrip = $reportType === 'transkrip';
$isStudentScope = $scope === 'student';

if (isset($_GET['generate'])) {
    if ($isStudentScope && !$studentId) {
        $errors[] = 'Pilih siswa untuk laporan individu.';
    }

    if (!$isStudentScope && !$classId) {
        $errors[] = 'Pilih kelas untuk laporan per kelas.';
    }

    if ($isTranskrip) {
        if (!$period) {
            $errors[] = 'Pilih periode untuk laporan transkrip.';
        }
    } elseif (!$tahunAjaranId) {
        $errors[] = 'Pilih tahun ajaran/semester untuk laporan UTS/UAS.';
    }

    $periodStart = '';
    $periodEnd = '';
    if ($isTranskrip && $period) {
        $parts = explode('-', $period);
        if (count($parts) === 2 && ctype_digit($parts[0]) && ctype_digit($parts[1])) {
            $periodStart = $parts[0];
            $periodEnd = $parts[1];
        } else {
            $errors[] = 'Format periode tidak valid.';
        }
    }

    if (empty($errors)) {
        $basePath = $urlPrefix . '/nilai/print-uts';
        if ($reportType === 'uas') {
            $basePath = $urlPrefix . '/nilai/print-uas';
        }
        if ($reportType === 'transkrip') {
            $basePath = $urlPrefix . '/nilai/print-transkrip';
        }

        if (!$isStudentScope) {
            $basePath .= '-class';
        }

        $params = [];
        if ($isStudentScope) {
            $params['student_id'] = $studentId;
        } else {
            $params['class_id'] = $classId;
        }

        if ($isTranskrip) {
            $params['period_start'] = $periodStart;
            $params['period_end'] = $periodEnd;
        } else {
            $params['tahun_ajaran_id'] = $tahunAjaranId;
        }

        $reportUrl = $basePath . '?' . http_build_query($params);
    }
}

ob_start();
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-primary-700">Laporan Nilai</h1>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="mdi:arrow-left" width="20" height="20"></iconify-icon>
            Kembali ke Nilai
        </a>
    </div>

    <div class="bg-white rounded-lg border border-secondary-200 shadow-sm p-6">
        <?php if (!empty($errors)): ?>
            <div class="mb-6 rounded-lg border border-status-error-200 bg-status-error-100 text-status-error-700 px-4 py-3">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:alert-circle" width="20" height="20"></iconify-icon>
                    <span>Periksa kembali pilihan laporan.</span>
                </div>
                <ul class="mt-2 text-sm text-status-error-700">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="GET" action="" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="report" class="block text-sm font-medium text-secondary-600 mb-2">Jenis Laporan</label>
                    <select id="report" name="report"
                            class="w-full bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200">
                        <option value="uts" <?= $reportType === 'uts' ? 'selected' : '' ?>>Ulangan Tengah Semester (UTS)</option>
                        <option value="uas" <?= $reportType === 'uas' ? 'selected' : '' ?>>Ulangan Akhir Semester (UAS)</option>
                        <option value="transkrip" <?= $reportType === 'transkrip' ? 'selected' : '' ?>>Transkrip Nilai</option>
                    </select>
                </div>
                <div>
                    <label for="scope" class="block text-sm font-medium text-secondary-600 mb-2">Cakupan Laporan</label>
                    <select id="scope" name="scope"
                            class="w-full bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200">
                        <option value="student" <?= $isStudentScope ? 'selected' : '' ?>>Per Siswa</option>
                        <option value="class" <?= !$isStudentScope ? 'selected' : '' ?>>Per Kelas</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-secondary-600 mb-2">Siswa</label>
                    <select id="student_id" name="student_id" <?= $isStudentScope ? '' : 'disabled' ?>
                            class="w-full bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 <?= $isStudentScope ? '' : 'opacity-60' ?>">
                        <option value="">Pilih Siswa</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= htmlspecialchars($student['id']) ?>" <?= $studentId === (int)$student['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($student['nama']) ?><?= $student['nis'] ? ' (NIS ' . htmlspecialchars($student['nis']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-secondary-500 mt-2">Gunakan untuk laporan individu.</p>
                </div>
                <div>
                    <label for="class_id" class="block text-sm font-medium text-secondary-600 mb-2">Kelas</label>
                    <select id="class_id" name="class_id" <?= $isStudentScope ? 'disabled' : '' ?>
                            class="w-full bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 <?= $isStudentScope ? 'opacity-60' : '' ?>">
                        <option value="">Pilih Kelas</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= htmlspecialchars($class['id']) ?>" <?= $classId === (int)$class['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-secondary-500 mt-2">Gunakan untuk laporan per kelas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tahun_ajaran_id" class="block text-sm font-medium text-secondary-600 mb-2">Tahun Ajaran / Semester</label>
                    <select id="tahun_ajaran_id" name="tahun_ajaran_id" <?= $isTranskrip ? 'disabled' : '' ?>
                            class="w-full bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 <?= $isTranskrip ? 'opacity-60' : '' ?>">
                        <option value="">Pilih Tahun Ajaran</option>
                        <?php foreach ($tahunAjaran as $ta): ?>
                            <option value="<?= htmlspecialchars($ta['id']) ?>" <?= $tahunAjaranId === (int)$ta['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ta['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-secondary-500 mt-2">Gunakan untuk laporan UTS/UAS.</p>
                </div>
                <div>
                    <label for="period" class="block text-sm font-medium text-secondary-600 mb-2">Periode (Tahun)</label>
                    <select id="period" name="period" <?= $isTranskrip ? '' : 'disabled' ?>
                            class="w-full bg-secondary-50 rounded-lg px-4 py-3 border border-secondary-200 <?= $isTranskrip ? '' : 'opacity-60' ?>">
                        <option value="">Pilih Periode</option>
                        <?php foreach ($periods as $periodItem): ?>
                            <?php $periodValue = $periodItem['tahun_mulai'] . '-' . $periodItem['tahun_selesai']; ?>
                            <option value="<?= htmlspecialchars($periodValue) ?>" <?= $period === $periodValue ? 'selected' : '' ?>>
                                <?= htmlspecialchars($periodItem['tahun_mulai']) ?>/<?= htmlspecialchars($periodItem['tahun_selesai']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-secondary-500 mt-2">Gunakan untuk laporan transkrip.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                <button type="submit" name="generate" value="1"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                    <iconify-icon icon="solar:document-text-linear" width="20" height="20"></iconify-icon>
                    Buat Laporan
                </button>
                <?php if ($reportUrl): ?>
                    <a href="<?= htmlspecialchars($reportUrl) ?>" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
                        <iconify-icon icon="solar:printer-linear" width="20" height="20"></iconify-icon>
                        Buka Laporan
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bg-secondary-50 border border-secondary-200 rounded-lg p-4 text-sm text-secondary-600">
        <div class="flex items-start gap-2">
            <iconify-icon icon="solar:info-circle-linear" width="18" height="18" class="mt-0.5"></iconify-icon>
            <div>
                Laporan dibuka dalam tab baru untuk dicetak atau disimpan sebagai PDF melalui fitur cetak browser.
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>
