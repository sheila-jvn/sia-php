<?php
$pageTitle = "Status Pembayaran SPP";
$currentPage = 'spp-students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Get student ID and year from query parameters
$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$yearId = isset($_GET['year']) ? (int)$_GET['year'] : 0;

if (!$studentId) {
    header("Location: spp-students");
    exit();
}

// Get student information
$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: spp-students");
    exit();
}

// Get academic year information
if ($yearId) {
    $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran WHERE id = ?");
    $stmt->execute([$yearId]);
    $year = $stmt->fetch();
} else {
    // Get current academic year
    $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC LIMIT 1");
    $stmt->execute();
    $year = $stmt->fetch();
    $yearId = $year['id'];
}

if (!$year) {
    header("Location: spp-students");
    exit();
}

// Get all academic years for dropdown
$stmt = $pdo->prepare("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC");
$stmt->execute();
$allYears = $stmt->fetchAll();

// Get all payments for this student and year
$stmt = $pdo->prepare("
    SELECT * FROM pembayaran_spp 
    WHERE id_siswa = ? AND id_tahun_ajaran = ? 
    ORDER BY bulan, tanggal_bayar
");
$stmt->execute([$studentId, $yearId]);
$payments = $stmt->fetchAll();

// Define months in order
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Fixed SPP amount per month
$sppAmount = 650000;

// Group payments by month and calculate totals
$monthlyData = [];
foreach ($months as $month) {
    $monthPayments = array_filter($payments, function($p) use ($month) {
        return $p['bulan'] === $month;
    });
    
    $totalPaid = array_sum(array_column($monthPayments, 'jumlah_bayar'));
    $outstanding = max(0, $sppAmount - $totalPaid);
    $status = $outstanding > 0 ? 'Belum Lunas' : 'Lunas';
    
    $monthlyData[$month] = [
        'payments' => $monthPayments,
        'total_paid' => $totalPaid,
        'outstanding' => $outstanding,
        'status' => $status
    ];
}

ob_start();
?>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-primary-800 mb-2">Status Pembayaran SPP</h1>
            <h2 class="text-xl text-secondary-600"><?= htmlspecialchars($student['nama']) ?> (NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?>)</h2>
        </div>
        <a href="spp-students" 
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="solar:arrow-left-linear" width="20" height="20"></iconify-icon>
            Kembali
        </a>
    </div>

    <!-- Academic Year Selection -->
    <div class="bg-white rounded-lg shadow-md border border-secondary-200 mb-6">
        <div class="p-6">
            <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
                <input type="hidden" name="id" value="<?= $studentId ?>">
                <div class="flex-1 max-w-md">
                    <label for="year" class="block text-sm font-medium text-secondary-700 mb-2">Tahun Ajaran</label>
                    <select name="year" id="year" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                            onchange="this.form.submit()">
                        <?php foreach ($allYears as $y): ?>
                            <option value="<?= $y['id'] ?>" <?= $y['id'] == $yearId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($y['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Payment Status -->
    <div class="bg-white rounded-lg shadow-md border border-secondary-200 mb-6">
        <div class="px-6 py-4 border-b border-secondary-200">
            <h2 class="text-xl font-semibold text-secondary-800">Status Pembayaran SPP - <?= htmlspecialchars($year['nama']) ?></h2>
            <p class="text-sm text-secondary-600 mt-1">SPP per bulan: Rp <?= number_format($sppAmount, 0, ',', '.') ?></p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-primary-100 text-primary-700">
                        <tr>
                            <th class="px-4 py-2 font-semibold">Bulan</th>
                            <th class="px-4 py-2 font-semibold">Riwayat Cicilan</th>
                            <th class="px-4 py-2 font-semibold">Total Dibayar</th>
                            <th class="px-4 py-2 font-semibold">Sisa</th>
                            <th class="px-4 py-2 font-semibold">Status</th>
                            <th class="px-4 py-2 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-secondary-200">
                        <?php foreach ($months as $month): 
                            $data = $monthlyData[$month];
                        ?>
                            <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                                <td class="px-4 py-3 font-medium text-secondary-900 align-middle"><?= htmlspecialchars($month) ?></td>
                                <td class="px-4 py-3 align-middle">
                                    <?php if (empty($data['payments'])): ?>
                                        <span class="text-secondary-500">Belum ada pembayaran</span>
                                    <?php else: ?>
                                        <div class="space-y-1">
                                            <?php foreach ($data['payments'] as $payment): ?>
                                                <div class="text-sm text-secondary-700">
                                                    <?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?>: 
                                                    <span class="font-medium">Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-medium text-secondary-900 align-middle">
                                    Rp <?= number_format($data['total_paid'], 0, ',', '.') ?>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <?php if ($data['outstanding'] > 0): ?>
                                        <span class="text-status-error-600 font-medium">
                                            Rp <?= number_format($data['outstanding'], 0, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-status-success-600 font-medium">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <?php if ($data['status'] === 'Lunas'): ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-status-success-100 text-status-success-700">
                                            Lunas
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-status-warning-100 text-status-warning-700">
                                            Belum Lunas
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <?php if ($data['outstanding'] > 0): ?>
                                            <a href="spp-pay?student_id=<?= $studentId ?>&year_id=<?= $yearId ?>&month=<?= urlencode($month) ?>" 
                                               class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                                                <iconify-icon icon="solar:add-circle-linear" width="16" height="16"></iconify-icon>
                                                Bayar Cicil
                                            </a>
                                        <?php else: ?>
                                            <button class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-lg border border-secondary-300 text-secondary-500 cursor-not-allowed" 
                                                    disabled>
                                                <iconify-icon icon="solar:check-circle-bold" width="16" height="16"></iconify-icon>
                                                Lunas
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($data['payments'])): ?>
                                            <a class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-lg border border-accent-300 text-accent-700 bg-white hover:bg-accent-50 transition" 
                                               href="spp-print-receipt.php?student_id=<?= $studentId ?>&year_id=<?= $yearId ?>&month=<?= urlencode($month) ?>" target="_blank">
                                                <iconify-icon icon="solar:printer-linear" width="16" height="16"></iconify-icon>
                                                Print
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Summary -->
            <?php 
            $totalPaidAllMonths = array_sum(array_column($monthlyData, 'total_paid'));
            $totalOutstandingAllMonths = array_sum(array_column($monthlyData, 'outstanding'));
            $lunasBulan = count(array_filter($monthlyData, function($data) { return $data['status'] === 'Lunas'; }));
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <div class="bg-secondary-50 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-secondary-700 mb-2">Total Dibayar</h3>
                    <div class="text-2xl font-bold text-status-success-600">Rp <?= number_format($totalPaidAllMonths, 0, ',', '.') ?></div>
                </div>
                <div class="bg-secondary-50 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-secondary-700 mb-2">Total Sisa</h3>
                    <div class="text-2xl font-bold text-status-error-600">Rp <?= number_format($totalOutstandingAllMonths, 0, ',', '.') ?></div>
                </div>
                <div class="bg-secondary-50 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-secondary-700 mb-2">Bulan Lunas</h3>
                    <div class="text-2xl font-bold text-accent-600"><?= $lunasBulan ?> / 12</div>
                </div>
                <div class="bg-secondary-50 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-secondary-700 mb-2">Progress</h3>
                    <div class="text-2xl font-bold text-primary-600"><?= number_format(($lunasBulan / 12) * 100, 1) ?>%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt(month) {
    alert('Print receipt untuk bulan ' + month + ' (fitur print akan dikembangkan)');
}
</script>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>