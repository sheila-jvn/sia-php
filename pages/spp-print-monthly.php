<?php
$pageTitle = "Laporan Bulanan SPP";

require_once __DIR__ . '/../lib/database.php';

// Get month and year from GET or default to current
$month = isset($_GET['month']) ? $_GET['month'] : date('F');
$yearNum = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Convert English month to Indonesian if needed
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$monthMap = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
    'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
    'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
if (isset($monthMap[$month])) $month = $monthMap[$month];
if (!in_array($month, $months)) $month = $months[(int)date('n')-1];

$pdo = getDbConnection();

// Get all payments for this month and year
$stmt = $pdo->prepare('SELECT ps.*, s.nis, s.nama as nama_siswa, ta.nama as tahun_ajaran FROM pembayaran_spp ps JOIN siswa s ON ps.id_siswa = s.id JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id WHERE ps.bulan = ? AND YEAR(ps.tanggal_bayar) = ? ORDER BY s.nis ASC, ps.tanggal_bayar ASC');
$stmt->execute([$month, $yearNum]);
$payments = $stmt->fetchAll();

ob_start();
?>

<style>
    @media print { .no-print { display: none !important; } }
</style>

<div class="max-w-4xl mx-auto my-8 p-8 border border-secondary-300 rounded-lg shadow-lg bg-white">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-800">Laporan Pembayaran SPP Bulan <?= htmlspecialchars($month) ?> <?= $yearNum ?></h1>
        <button onclick="window.print()" class="no-print px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Print</button>
    </div>
    <div class="mb-4">
        <table class="min-w-full text-sm border border-secondary-200 rounded">
            <thead class="bg-secondary-100">
                <tr>
                    <th class="px-3 py-2 border-b border-secondary-200 text-left">NIS</th>
                    <th class="px-3 py-2 border-b border-secondary-200 text-left">Nama Siswa</th>
                    <th class="px-3 py-2 border-b border-secondary-200 text-left">Tahun Ajaran</th>
                    <th class="px-3 py-2 border-b border-secondary-200 text-left">Tanggal Bayar</th>
                    <th class="px-3 py-2 border-b border-secondary-200 text-left">Jumlah Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['nis']) ?></td>
                        <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['nama_siswa']) ?></td>
                        <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['tahun_ajaran']) ?></td>
                        <td class="px-3 py-2 border-b border-secondary-100"><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></td>
                        <td class="px-3 py-2 border-b border-secondary-100">Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-secondary-500">Tidak ada pembayaran bulan ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-8 text-right text-xs text-secondary-500">
        Dicetak pada <?= date('d/m/Y H:i') ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
?>
