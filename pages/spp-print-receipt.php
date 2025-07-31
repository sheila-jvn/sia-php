<?php
$pageTitle = "Cetak Kwitansi SPP";
$currentPage = 'spp-students';

require_once __DIR__ . '/../lib/database.php';

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$yearId = isset($_GET['year_id']) ? (int)$_GET['year_id'] : 0;
$month = isset($_GET['month']) ? $_GET['month'] : '';

if (!$studentId || !$yearId || !$month) {
    die('Parameter tidak lengkap.');
}

$pdo = getDbConnection();

// Get student info
$stmt = $pdo->prepare('SELECT * FROM siswa WHERE id = ?');
$stmt->execute([$studentId]);
$student = $stmt->fetch();
if (!$student) die('Siswa tidak ditemukan.');

// Get year info
$stmt = $pdo->prepare('SELECT * FROM tahun_ajaran WHERE id = ?');
$stmt->execute([$yearId]);
$year = $stmt->fetch();
if (!$year) die('Tahun ajaran tidak ditemukan.');

// Get payments for this month
$stmt = $pdo->prepare('SELECT * FROM pembayaran_spp WHERE id_siswa = ? AND id_tahun_ajaran = ? AND bulan = ? ORDER BY tanggal_bayar');
$stmt->execute([$studentId, $yearId, $month]);
$payments = $stmt->fetchAll();
if (!$payments) die('Tidak ada pembayaran untuk bulan ini.');

$sppAmount = 650000;
$totalPaid = array_sum(array_column($payments, 'jumlah_bayar'));
$outstanding = max(0, $sppAmount - $totalPaid);

ob_start();
?>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-container { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>

<div class="max-w-2xl mx-auto p-6">
    <div class="print-container bg-white border border-secondary-300 rounded-lg shadow-lg p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary-800">Kwitansi Pembayaran SPP</h1>
            <button onclick="window.print()" 
                    class="no-print inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                <iconify-icon icon="solar:printer-linear" width="20" height="20"></iconify-icon>
                Print
            </button>
        </div>
        
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-secondary-600">Nama Siswa</span>
                    <div class="text-base text-secondary-900"><?= htmlspecialchars($student['nama']) ?></div>
                </div>
                <div>
                    <span class="text-sm font-medium text-secondary-600">NIS</span>
                    <div class="text-base text-secondary-900"><?= htmlspecialchars($student['nis'] ?? '-') ?></div>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-secondary-600">Tahun Ajaran</span>
                    <div class="text-base text-secondary-900"><?= htmlspecialchars($year['nama']) ?></div>
                </div>
                <div>
                    <span class="text-sm font-medium text-secondary-600">Bulan</span>
                    <div class="text-base text-secondary-900"><?= htmlspecialchars($month) ?></div>
                </div>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-secondary-800 mb-3">Detail Pembayaran</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-secondary-200 rounded-lg">
                    <thead class="bg-secondary-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-secondary-700 border-b border-secondary-200">Tanggal Bayar</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-secondary-700 border-b border-secondary-200">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-secondary-200">
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-secondary-900"><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></td>
                                <td class="px-4 py-3 text-sm text-secondary-900 text-right font-medium">Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="border-t border-secondary-200 pt-4 space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-secondary-600">SPP per Bulan:</span>
                <span class="text-base font-medium text-secondary-900">Rp <?= number_format($sppAmount, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-secondary-600">Total Dibayar:</span>
                <span class="text-base font-semibold text-status-success-600">Rp <?= number_format($totalPaid, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-secondary-600">Sisa:</span>
                <span class="text-base font-semibold <?= $outstanding > 0 ? 'text-status-error-600' : 'text-status-success-600' ?>">
                    <?= $outstanding > 0 ? 'Rp ' . number_format($outstanding, 0, ',', '.') : 'Lunas' ?>
                </span>
            </div>
        </div>
        
        <div class="mt-8 pt-4 border-t border-secondary-200 text-right">
            <div class="text-xs text-secondary-500">
                Dicetak pada <?= date('d/m/Y H:i') ?>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
?>
