<?php
$pageTitle = "Laporan & Export SPP";
$currentPage = 'spp-reports';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Get summary statistics
// Total students
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM siswa");
$stmt->execute();
$totalStudents = $stmt->fetchColumn();

// Total payments this month
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(jumlah_bayar) as amount FROM pembayaran_spp WHERE MONTH(tanggal_bayar) = MONTH(CURDATE()) AND YEAR(tanggal_bayar) = YEAR(CURDATE())");
$stmt->execute();
$thisMonth = $stmt->fetch();

// Total payments this year
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(jumlah_bayar) as amount FROM pembayaran_spp WHERE YEAR(tanggal_bayar) = YEAR(CURDATE())");
$stmt->execute();
$thisYear = $stmt->fetch();

// Students with unpaid SPP (students who haven't paid any SPP this year)
$stmt = $pdo->prepare("
    SELECT COUNT(*) as unpaid 
    FROM siswa s 
    WHERE NOT EXISTS (
        SELECT 1 FROM pembayaran_spp ps 
        WHERE ps.id_siswa = s.id 
        AND YEAR(ps.tanggal_bayar) = YEAR(CURDATE())
    )
");
$stmt->execute();
$unpaidStudents = $stmt->fetchColumn();

// Recent payments (last 10)
$stmt = $pdo->prepare("
    SELECT ps.*, s.nis, s.nama as nama_siswa, ta.nama as tahun_ajaran
    FROM pembayaran_spp ps
    JOIN siswa s ON ps.id_siswa = s.id
    JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id
    ORDER BY ps.tanggal_bayar DESC, ps.id DESC
    LIMIT 10
");
$stmt->execute();
$recentPayments = $stmt->fetchAll();

ob_start();
?>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-primary-800">Laporan & Export SPP</h1>
        <a href="spp-students" 
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="solar:arrow-left-linear" width="20" height="20"></iconify-icon>
            Kembali
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg shadow-md">
            <div class="p-6 text-center">
                <div class="text-3xl font-bold"><?= number_format($totalStudents, 0, ',', '.') ?></div>
                <p class="text-primary-100 mt-2">Total Siswa</p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-status-success-500 to-status-success-600 text-white rounded-lg shadow-md">
            <div class="p-6 text-center">
                <div class="text-lg font-semibold"><?= number_format($thisMonth['total'] ?? 0, 0, ',', '.') ?> Transaksi</div>
                <div class="text-xl font-bold">Rp <?= number_format($thisMonth['amount'] ?? 0, 0, ',', '.') ?></div>
                <p class="text-status-success-100 mt-2">Bulan Ini</p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-accent-500 to-accent-600 text-white rounded-lg shadow-md">
            <div class="p-6 text-center">
                <div class="text-lg font-semibold"><?= number_format($thisYear['total'] ?? 0, 0, ',', '.') ?> Transaksi</div>
                <div class="text-xl font-bold">Rp <?= number_format($thisYear['amount'] ?? 0, 0, ',', '.') ?></div>
                <p class="text-accent-100 mt-2">Tahun Ini</p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-status-warning-500 to-status-warning-600 text-white rounded-lg shadow-md">
            <div class="p-6 text-center">
                <div class="text-3xl font-bold"><?= number_format($unpaidStudents, 0, ',', '.') ?></div>
                <p class="text-status-warning-100 mt-2">Belum Lunas</p>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md border border-secondary-200">
            <div class="px-6 py-4 border-b border-secondary-200">
                <h2 class="text-xl font-semibold text-secondary-800">Export Data Summary</h2>
            </div>
            <div class="p-6">
                <p class="text-secondary-600 mb-4">Export ringkasan transaksi pembayaran SPP yang telah dilakukan</p>
                <a href="<?= $urlPrefix ?>/spp-export-summary" class="w-full inline-flex items-center justify-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
                    <iconify-icon icon="solar:file-smile-linear" width="20" height="20"></iconify-icon>
                    Download Transaksi CSV
                </a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md border border-secondary-200">
            <div class="px-6 py-4 border-b border-secondary-200">
                <h2 class="text-xl font-semibold text-secondary-800">Export Data Detail</h2>
            </div>
            <div class="p-6">
                <p class="text-secondary-600 mb-4">Export detail lengkap status pembayaran SPP per siswa per bulan</p>
                <a href="<?= $urlPrefix ?>/spp-export-detail" class="w-full inline-flex items-center justify-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
                    <iconify-icon icon="solar:file-text-linear" width="20" height="20"></iconify-icon>
                    Download Status Detail CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md border border-secondary-200 mb-6">
        <div class="px-6 py-4 border-b border-secondary-200">
            <h2 class="text-xl font-semibold text-secondary-800">Aksi Cepat</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="spp-students" 
                   class="flex flex-col items-center p-4 border border-secondary-200 rounded-lg hover:bg-primary-50 hover:border-primary-300 transition-colors text-center">
                    <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-3xl text-primary-600 mb-2"></iconify-icon>
                    <span class="text-sm font-medium text-secondary-700">Daftar Siswa</span>
                </a>
                <a href="spp-history" 
                   class="flex flex-col items-center p-4 border border-secondary-200 rounded-lg hover:bg-accent-50 hover:border-accent-300 transition-colors text-center">
                    <iconify-icon icon="solar:history-linear" class="text-3xl text-accent-600 mb-2"></iconify-icon>
                    <span class="text-sm font-medium text-secondary-700">Riwayat Cicilan</span>
                </a>
                <a href="<?= $urlPrefix ?>/spp-print-monthly?month=<?= urlencode((new DateTime())->format('F')) ?>&year=<?= date('Y') ?>" target="_blank"
    class="flex flex-col items-center p-4 border border-secondary-200 rounded-lg hover:bg-status-success-50 hover:border-status-success-300 transition-colors text-center">
    <iconify-icon icon="solar:printer-linear" class="text-3xl text-status-success-600 mb-2"></iconify-icon>
    <span class="text-sm font-medium text-secondary-700">Laporan Bulanan</span>
</a>
                <a href="<?= $urlPrefix ?>/spp-print-yearly?year=<?= date('Y') ?>" target="_blank"
    class="flex flex-col items-center p-4 border border-secondary-200 rounded-lg hover:bg-status-warning-50 hover:border-status-warning-300 transition-colors text-center">
    <iconify-icon icon="solar:document-text-linear" class="text-3xl text-status-warning-600 mb-2"></iconify-icon>
    <span class="text-sm font-medium text-secondary-700">Laporan Tahunan</span>
</a>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-lg shadow-md border border-secondary-200">
        <div class="px-6 py-4 border-b border-secondary-200">
            <h2 class="text-xl font-semibold text-secondary-800">Pembayaran Terbaru</h2>
        </div>
        <div class="p-6">
            <?php if (empty($recentPayments)): ?>
                <div class="bg-accent-100 border border-accent-200 text-accent-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <iconify-icon icon="solar:info-circle-bold" class="mr-2 text-lg"></iconify-icon>
                        Belum ada data pembayaran.
                    </div>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-primary-100 text-primary-700">
                            <tr>
                                <th class="px-4 py-2 font-semibold">Tanggal</th>
                                <th class="px-4 py-2 font-semibold">NIS</th>
                                <th class="px-4 py-2 font-semibold">Nama Siswa</th>
                                <th class="px-4 py-2 font-semibold">Tahun Ajaran</th>
                                <th class="px-4 py-2 font-semibold">Bulan</th>
                                <th class="px-4 py-2 font-semibold">Jumlah</th>
                                <th class="px-4 py-2 font-semibold">ID</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-secondary-200">
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['nis'] ?? '-') ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['nama_siswa']) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['tahun_ajaran']) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['bulan']) ?></td>
                                    <td class="px-4 py-3 text-right font-medium text-secondary-900 align-middle">Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3 align-middle">
                                        <code class="bg-secondary-100 text-secondary-800 px-2 py-1 rounded text-xs">
                                            #<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?>
                                        </code>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-6">
                    <a href="spp-history" 
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-primary-300 text-primary-700 bg-white hover:bg-primary-50 transition">
                        <iconify-icon icon="solar:eye-linear" width="20" height="20"></iconify-icon>
                        Lihat Semua Riwayat
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>