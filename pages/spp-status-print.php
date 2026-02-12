<?php
$pageTitle = 'Laporan Status Pembayaran SPP';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0);
$yearParam = $_GET['year'] ?? 0;
$viewAllYears = ($yearParam === 'all');

$errorMessage = '';
$student = null;
$year = null;
$allYears = [];
$payments = [];
$monthlyData = [];
$yearlyData = [];

if (!$studentId) {
    $errorMessage = 'Parameter siswa tidak lengkap.';
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT * FROM siswa WHERE id = ?');
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    if (!$student) {
        $errorMessage = 'Data siswa tidak ditemukan.';
    }
}

if (!$errorMessage) {
    $stmt = $pdo->prepare('SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC');
    $stmt->execute();
    $allYears = $stmt->fetchAll();
    if (empty($allYears)) {
        $errorMessage = 'Data tahun ajaran tidak ditemukan.';
    }
}

if (!$errorMessage) {
    if ($viewAllYears) {
        $yearId = 'all';
    } elseif ($yearParam) {
        $yearId = (int)$yearParam;
        $stmt = $pdo->prepare('SELECT * FROM tahun_ajaran WHERE id = ?');
        $stmt->execute([$yearId]);
        $year = $stmt->fetch();
        if (!$year) {
            $errorMessage = 'Data tahun ajaran tidak ditemukan.';
        }
    } else {
        $year = $allYears[0];
        $yearId = $year['id'];
    }
}

if (!$errorMessage) {
    if ($viewAllYears) {
        $stmt = $pdo->prepare('
            SELECT ps.*, ta.nama as tahun_ajaran_nama, ta.id as tahun_ajaran_id
            FROM pembayaran_spp ps
            JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id
            WHERE ps.id_siswa = ?
            ORDER BY ta.tahun_mulai DESC, ps.bulan, ps.tanggal_bayar
        ');
        $stmt->execute([$studentId]);
        $payments = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare('
            SELECT * FROM pembayaran_spp
            WHERE id_siswa = ? AND id_tahun_ajaran = ?
            ORDER BY bulan, tanggal_bayar
        ');
        $stmt->execute([$studentId, $yearId]);
        $payments = $stmt->fetchAll();
    }
}

$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$sppAmount = 650000;

if (!$errorMessage) {
    if ($viewAllYears) {
        foreach ($allYears as $y) {
            $yearIdKey = $y['id'];
            $yearlyData[$yearIdKey] = [
                'year_name' => $y['nama'],
                'months' => []
            ];

            foreach ($months as $month) {
                $monthPayments = array_filter($payments, function ($payment) use ($month, $yearIdKey) {
                    return $payment['bulan'] === $month && $payment['tahun_ajaran_id'] == $yearIdKey;
                });

                $totalPaid = array_sum(array_column($monthPayments, 'jumlah_bayar'));
                $outstanding = max(0, $sppAmount - $totalPaid);
                $status = $outstanding > 0 ? 'Belum Lunas' : 'Lunas';

                $yearlyData[$yearIdKey]['months'][$month] = [
                    'payments' => $monthPayments,
                    'total_paid' => $totalPaid,
                    'outstanding' => $outstanding,
                    'status' => $status
                ];
            }
        }
    } else {
        foreach ($months as $month) {
            $monthPayments = array_filter($payments, function ($payment) use ($month) {
                return $payment['bulan'] === $month;
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
    }
}

ob_start();
?>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-container { box-shadow: none !important; border: 1px solid #000 !important; }
    @page { size: A4 portrait; margin: 10mm; }
}
.table-spp {
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
}
.table-spp th,
.table-spp td {
    border: 1px solid #374151;
    padding: 6px 8px;
    vertical-align: middle;
}
.table-spp th {
    background-color: #f3f4f6;
    font-weight: 600;
    text-align: center;
}
.table-spp tbody tr:nth-child(even) { background-color: #f9fafb; }
.table-spp .text-left { text-align: left; }
.table-spp .text-center { text-align: center; }
.table-spp .text-right { text-align: right; }
</style>

<div class="max-w-5xl mx-auto p-6">
    <div class="print-container bg-white border border-secondary-300 rounded-lg shadow-lg p-6 space-y-6">
        <?php require __DIR__ . '/_kop-surat.php'; ?>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-secondary-900">Laporan Status Pembayaran SPP</h1>
                <p class="text-sm text-secondary-600">
                    Periode: <?= $viewAllYears ? 'Semua Tahun Ajaran' : htmlspecialchars($year['nama'] ?? '-') ?>
                </p>
                <p class="text-sm text-secondary-600">SPP per Bulan: Rp <?= number_format($sppAmount, 0, ',', '.') ?></p>
            </div>
            <button onclick="window.print()"
                    class="no-print inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
                <iconify-icon icon="solar:printer-linear" width="20" height="20"></iconify-icon>
                Cetak PDF
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
            </div>

            <?php if ($viewAllYears): ?>
                <?php foreach ($yearlyData as $yearData): ?>
                    <div class="space-y-3">
                        <div class="text-base font-semibold text-secondary-800">
                            Tahun Ajaran: <?= htmlspecialchars($yearData['year_name']) ?>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-spp">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th style="width: 120px;">Bulan</th>
                                        <th>Riwayat Pembayaran</th>
                                        <th style="width: 130px;">Total Dibayar</th>
                                        <th style="width: 110px;">Sisa</th>
                                        <th style="width: 110px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($months as $index => $month):
                                        $data = $yearData['months'][$month];
                                    ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $index + 1 ?></td>
                                            <td class="text-left align-middle"><?= htmlspecialchars($month) ?></td>
                                            <td class="text-left align-middle">
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
                                            <td class="text-right align-middle">
                                                Rp <?= number_format($data['total_paid'], 0, ',', '.') ?>
                                            </td>
                                            <td class="text-right align-middle">
                                                <?php if ($data['outstanding'] > 0): ?>
                                                    <span class="text-status-error-600 font-medium">
                                                        Rp <?= number_format($data['outstanding'], 0, ',', '.') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-status-success-600 font-medium">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if ($data['status'] === 'Lunas'): ?>
                                                    <span class="text-status-success-600 font-medium">Lunas</span>
                                                <?php else: ?>
                                                    <span class="text-status-warning-600 font-medium">Belum Lunas</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-spp">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th style="width: 120px;">Bulan</th>
                                <th>Riwayat Pembayaran</th>
                                <th style="width: 130px;">Total Dibayar</th>
                                <th style="width: 110px;">Sisa</th>
                                <th style="width: 110px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($months as $index => $month):
                                $data = $monthlyData[$month];
                            ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $index + 1 ?></td>
                                    <td class="text-left align-middle"><?= htmlspecialchars($month) ?></td>
                                    <td class="text-left align-middle">
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
                                    <td class="text-right align-middle">
                                        Rp <?= number_format($data['total_paid'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-right align-middle">
                                        <?php if ($data['outstanding'] > 0): ?>
                                            <span class="text-status-error-600 font-medium">
                                                Rp <?= number_format($data['outstanding'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-status-success-600 font-medium">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($data['status'] === 'Lunas'): ?>
                                            <span class="text-status-success-600 font-medium">Lunas</span>
                                        <?php else: ?>
                                            <span class="text-status-warning-600 font-medium">Belum Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php
            if ($viewAllYears) {
                $totalPaidAllMonths = 0;
                $totalOutstandingAllMonths = 0;
                $totalLunasBulan = 0;
                $totalBulan = count($allYears) * 12;
                foreach ($yearlyData as $yData) {
                    foreach ($yData['months'] as $mData) {
                        $totalPaidAllMonths += $mData['total_paid'];
                        $totalOutstandingAllMonths += $mData['outstanding'];
                        if ($mData['status'] === 'Lunas') {
                            $totalLunasBulan++;
                        }
                    }
                }
            } else {
                $totalPaidAllMonths = array_sum(array_column($monthlyData, 'total_paid'));
                $totalOutstandingAllMonths = array_sum(array_column($monthlyData, 'outstanding'));
                $totalLunasBulan = count(array_filter($monthlyData, function ($data) { return $data['status'] === 'Lunas'; }));
                $totalBulan = 12;
            }
            ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-secondary-50 rounded-lg p-4 text-center border border-secondary-200">
                    <h3 class="text-sm font-semibold text-secondary-700 mb-2">Total Dibayar</h3>
                    <div class="text-lg font-bold text-status-success-600">Rp <?= number_format($totalPaidAllMonths, 0, ',', '.') ?></div>
                </div>
                <div class="bg-secondary-50 rounded-lg p-4 text-center border border-secondary-200">
                    <h3 class="text-sm font-semibold text-secondary-700 mb-2">Total Sisa</h3>
                    <div class="text-lg font-bold text-status-error-600">Rp <?= number_format($totalOutstandingAllMonths, 0, ',', '.') ?></div>
                </div>
                <div class="bg-secondary-50 rounded-lg p-4 text-center border border-secondary-200">
                    <h3 class="text-sm font-semibold text-secondary-700 mb-2">Bulan Lunas</h3>
                    <div class="text-lg font-bold text-accent-600"><?= $totalLunasBulan ?> / <?= $totalBulan ?></div>
                </div>
                <div class="bg-secondary-50 rounded-lg p-4 text-center border border-secondary-200">
                    <h3 class="text-sm font-semibold text-secondary-700 mb-2">Progress</h3>
                    <div class="text-lg font-bold text-primary-600">
                        <?= $totalBulan > 0 ? number_format(($totalLunasBulan / $totalBulan) * 100, 1) : '0' ?>%
                    </div>
                </div>
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
