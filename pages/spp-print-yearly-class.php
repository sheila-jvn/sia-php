<?php
$pageTitle = 'Rekap Pembayaran SPP';

require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/database.php';

// Constants - Fixed SPP amount per month (matches spp-status.php)
const MONTHLY_SPP_FEE = 650000;
const TOTAL_YEARLY_FEE = MONTHLY_SPP_FEE * 12;

// Get parameters
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$yearId = isset($_GET['year_id']) ? (int)$_GET['year_id'] : 0;

$pdo = getDbConnection();

$errorMessage = '';
$classInfo = null;
$students = [];
$payments = [];

// Get all classes for filter dropdown
$stmt = $pdo->prepare("SELECT k.id, k.nama, ta.nama as tahun_ajaran
                       FROM kelas k
                       JOIN tahun_ajaran ta ON k.id_tahun_ajaran = ta.id
                       ORDER BY ta.tahun_mulai DESC, k.nama ASC");
$stmt->execute();
$allClasses = $stmt->fetchAll();

// Get all academic years for filter dropdown
$stmt = $pdo->prepare("SELECT id, nama FROM tahun_ajaran ORDER BY tahun_mulai DESC");
$stmt->execute();
$allYears = $stmt->fetchAll();

// If no class_id provided, use first available class
if (!$classId && !empty($allClasses)) {
    $classId = $allClasses[0]['id'];
}

// If no year_id provided, try to get from selected class or use current year
if (!$yearId && $classId) {
    foreach ($allClasses as $cls) {
        if ($cls['id'] == $classId) {
            // Find matching year id from allYears
            foreach ($allYears as $yr) {
                if ($yr['nama'] === $cls['tahun_ajaran']) {
                    $yearId = $yr['id'];
                    break 2;
                }
            }
        }
    }
}

if (!$classId) {
    $errorMessage = 'Tidak ada data kelas yang tersedia.';
}

if (!$errorMessage) {
    // Get class information
    $stmt = $pdo->prepare("
        SELECT k.nama as class_name, k.id_tahun_ajaran, ta.nama as academic_year_name,
               ta.tahun_mulai, ta.tahun_selesai
        FROM kelas k
        JOIN tahun_ajaran ta ON k.id_tahun_ajaran = ta.id
        WHERE k.id = ?
    ");
    $stmt->execute([$classId]);
    $classInfo = $stmt->fetch();

    if (!$classInfo) {
        $errorMessage = 'Data kelas tidak ditemukan.';
    }
}

if (!$errorMessage && !$yearId) {
    // Use the class's academic year
    $yearId = $classInfo['id_tahun_ajaran'];
}

if (!$errorMessage) {
    // Try to get students from attendance records (kehadiran table)
    // If no attendance records exist, fall back to all students
    $stmt = $pdo->prepare("
        SELECT DISTINCT s.id, s.nis, s.nama as nama_siswa
        FROM siswa s
        JOIN kehadiran kh ON s.id = kh.id_siswa
        WHERE kh.id_kelas = ?
          AND kh.id_tahun_ajaran = ?
        ORDER BY s.nis ASC
    ");
    $stmt->execute([$classId, $yearId]);
    $students = $stmt->fetchAll();

    // If no students found via attendance, get all students who have SPP payments for this year
    if (empty($students)) {
        $stmt = $pdo->prepare("
            SELECT DISTINCT s.id, s.nis, s.nama as nama_siswa
            FROM siswa s
            JOIN pembayaran_spp ps ON s.id = ps.id_siswa
            WHERE ps.id_tahun_ajaran = ?
            ORDER BY s.nis ASC
        ");
        $stmt->execute([$yearId]);
        $students = $stmt->fetchAll();
    }

    // If still no students, get all students as final fallback
    if (empty($students)) {
        $stmt = $pdo->prepare("SELECT id, nis, nama as nama_siswa FROM siswa ORDER BY nis ASC");
        $stmt->execute();
        $students = $stmt->fetchAll();
    }

    if (empty($students)) {
        $errorMessage = 'Tidak ada data siswa yang tersedia.';
    }
}

if (!$errorMessage) {
    // Get all student IDs
    $studentIds = array_column($students, 'id');
    $placeholders = implode(',', array_fill(0, count($studentIds), '?'));

    // Get all SPP payments for these students in the academic year
    $stmt = $pdo->prepare("
        SELECT ps.id_siswa, ps.bulan, SUM(ps.jumlah_bayar) as total_bayar
        FROM pembayaran_spp ps
        WHERE ps.id_siswa IN ($placeholders)
          AND ps.id_tahun_ajaran = ?
        GROUP BY ps.id_siswa, ps.bulan
    ");
    $stmt->execute(array_merge($studentIds, [$yearId]));
    $paymentRows = $stmt->fetchAll();

    // Organize payments by student and month
    $payments = [];
    foreach ($paymentRows as $row) {
        $studentId = $row['id_siswa'];
        $month = $row['bulan'];
        if (!isset($payments[$studentId])) {
            $payments[$studentId] = [];
        }
        $payments[$studentId][$month] = (float)$row['total_bayar'];
    }
}

// Months array in academic year order (July - June)
$months = [
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
];

// Short month names for column headers
$shortMonths = ['Jul', 'Ags', 'Sep', 'Okt', 'Nop', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];

// Calculate totals for each student
$studentData = [];
$grandTotalPaid = 0;
$grandTotalBill = 0;
$grandTotalRemaining = 0;
$monthlyTotals = array_fill_keys($months, 0);

if (!$errorMessage) {
    foreach ($students as $index => $student) {
        $studentId = $student['id'];
        $monthlyPayments = [];
        $totalPaid = 0;

        foreach ($months as $month) {
            $amount = isset($payments[$studentId][$month]) ? $payments[$studentId][$month] : 0;
            $monthlyPayments[$month] = $amount;
            $totalPaid += $amount;
            $monthlyTotals[$month] += $amount;
        }

        $totalBill = TOTAL_YEARLY_FEE;
        $remaining = $totalBill - $totalPaid;

        $studentData[] = [
            'no' => $index + 1,
            'nis' => $student['nis'],
            'nama' => $student['nama_siswa'],
            'monthly_payments' => $monthlyPayments,
            'total_paid' => $totalPaid,
            'total_bill' => $totalBill,
            'remaining' => $remaining
        ];

        $grandTotalPaid += $totalPaid;
        $grandTotalBill += $totalBill;
        $grandTotalRemaining += $remaining;
    }
}

ob_start();
?>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-container { box-shadow: none !important; border: none !important; }
    @page { size: landscape; margin: 10mm; }
    .overflow-x-auto { overflow: visible !important; }
}
.table-spp {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
}
.table-spp th,
.table-spp td {
    border: 1px solid #374151;
    padding: 4px 6px;
    text-align: center;
}
.table-spp th {
    background-color: #f3f4f6;
    font-weight: 600;
}
.table-spp .text-left { text-align: left; }
.table-spp .text-right { text-align: right; }
.table-spp tbody tr:nth-child(even) { background-color: #f9fafb; }
.table-spp .grand-total {
    background-color: #e5e7eb !important;
    font-weight: 700;
}
</style>

<div class="max-w-7xl mx-auto p-4">
    <div class="print-container bg-white border border-secondary-300 rounded-lg shadow-lg p-6">

        <?php require __DIR__ . '/_kop-surat.php'; ?>

        <div class="text-center mb-4">
            <h1 class="text-lg font-bold text-secondary-900 uppercase tracking-wide">REKAPITULASI PEMBAYARAN SPP</h1>
            <p class="text-sm text-secondary-700 mt-1">Tahun Ajaran: <?= htmlspecialchars($classInfo['academic_year_name'] ?? '-') ?></p>
            <p class="text-sm text-secondary-700">Kelas: <?= htmlspecialchars($classInfo['class_name'] ?? '-') ?></p>
        </div>

        <!-- Filter Form (no-print) -->
        <div class="no-print mb-4 p-4 bg-secondary-50 rounded-lg border border-secondary-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-1">Kelas</label>
                    <select name="class_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm">
                        <?php foreach ($allClasses as $cls): ?>
                            <option value="<?= $cls['id'] ?>" <?= $cls['id'] == $classId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cls['nama'] . ' - ' . $cls['tahun_ajaran']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-1">Tahun Ajaran</label>
                    <select name="year_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg text-sm">
                        <?php foreach ($allYears as $yr): ?>
                            <option value="<?= $yr['id'] ?>" <?= $yr['id'] == $yearId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($yr['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition text-sm">
                        <iconify-icon icon="solar:magnifer-linear" width="16" height="16"></iconify-icon>
                        Tampilkan
                    </button>
                    <button onclick="window.print()" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition text-sm">
                        <iconify-icon icon="solar:printer-linear" width="16" height="16"></iconify-icon>
                        Cetak PDF
                    </button>
                </div>
            </form>
        </div>

        <?php if ($errorMessage): ?>
            <div class="rounded-lg border border-status-error-200 bg-status-error-100 text-status-error-700 px-4 py-3 mb-4">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-spp">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 30px;">No</th>
                            <th rowspan="2" style="width: 80px;">No Induk</th>
                            <th rowspan="2" style="width: 150px;">Nama Siswa</th>
                            <th colspan="12">Rincian Pembayaran</th>
                            <th rowspan="2" style="width: 80px;">Total<br>dibayar</th>
                            <th rowspan="2" style="width: 80px;">Total<br>Tagihan</th>
                            <th rowspan="2" style="width: 80px;">Sisa<br>Tagihan</th>
                        </tr>
                        <tr>
                            <?php foreach ($shortMonths as $shortMonth): ?>
                                <th style="width: 50px;"><?= $shortMonth ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($studentData)): ?>
                            <tr>
                                <td colspan="18" class="text-center py-4 text-secondary-500">
                                    Tidak ada data siswa untuk ditampilkan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($studentData as $data): ?>
                                <tr>
                                    <td><?= $data['no'] ?></td>
                                    <td class="text-left"><?= htmlspecialchars($data['nis'] ?? '-') ?></td>
                                    <td class="text-left"><?= htmlspecialchars($data['nama']) ?></td>
                                    <?php foreach ($months as $month): ?>
                                        <td class="text-right">
                                            <?php if ($data['monthly_payments'][$month] > 0): ?>
                                                <?= number_format($data['monthly_payments'][$month], 0, ',', '.') ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-right font-medium">
                                        <?= number_format($data['total_paid'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-right">
                                        <?= number_format($data['total_bill'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-right <?= $data['remaining'] > 0 ? 'text-status-error-600' : 'text-status-success-600' ?>">
                                        <?= number_format($data['remaining'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <!-- Grand Total Row -->
                            <tr class="grand-total">
                                <td colspan="3" class="text-left">TOTAL</td>
                                <?php foreach ($months as $month): ?>
                                    <td class="text-right">
                                        <?= $monthlyTotals[$month] > 0 ? number_format($monthlyTotals[$month], 0, ',', '.') : '-' ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-right"><?= number_format($grandTotalPaid, 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($grandTotalBill, 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($grandTotalRemaining, 0, ',', '.') ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="flex justify-between mt-8 px-8 no-print">
                <div class="text-center">
                    <p class="text-sm text-secondary-700">Pemimpin Yayasan</p>
                    <div class="mt-12 border-b border-secondary-400 w-48"></div>
                    <p class="text-sm text-secondary-600 mt-1">(_________________)</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-secondary-700">Bogor, <?= date('d F Y') ?></p>
                    <p class="text-sm text-secondary-700">Mengetahui</p>
                    <div class="mt-12 border-b border-secondary-400 w-48"></div>
                    <p class="text-sm text-secondary-600 mt-1">(_________________)</p>
                </div>
            </div>

            <!-- Print-only signature (visible when printing) -->
            <div class="hidden print:block">
                <div class="flex justify-between mt-8 px-8">
                    <div class="text-center">
                        <p class="text-sm text-secondary-700">Pemimpin Yayasan</p>
                        <div class="mt-12 border-b border-secondary-400 w-48"></div>
                        <p class="text-sm text-secondary-600 mt-1">(_________________)</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-secondary-700">Bogor, <?= date('d F Y') ?></p>
                        <p class="text-sm text-secondary-700">Mengetahui</p>
                        <div class="mt-12 border-b border-secondary-400 w-48"></div>
                        <p class="text-sm text-secondary-600 mt-1">(_________________)</p>
                    </div>
                </div>
            </div>

            <div class="text-right text-xs text-secondary-500 mt-4">
                Dicetak pada <?= date('d/m/Y H:i') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
