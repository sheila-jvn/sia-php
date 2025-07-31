<?php
$pageTitle = "Riwayat Cicilan SPP";
$currentPage = 'spp-history';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Get filter parameters
$filterStudent = isset($_GET['student']) ? (int)$_GET['student'] : 0;
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$filterMonth = isset($_GET['month']) ? $_GET['month'] : '';

// Define months
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Get all students for filter dropdown
$stmt = $pdo->prepare("SELECT id, nis, nama FROM siswa ORDER BY nis ASC");
$stmt->execute();
$allStudents = $stmt->fetchAll();

// Get all academic years for filter dropdown
$stmt = $pdo->prepare("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC");
$stmt->execute();
$allYears = $stmt->fetchAll();

// Build query with filters
$whereConditions = [];
$params = [];

if ($filterStudent > 0) {
    $whereConditions[] = "ps.id_siswa = ?";
    $params[] = $filterStudent;
}

if ($filterYear > 0) {
    $whereConditions[] = "ps.id_tahun_ajaran = ?";
    $params[] = $filterYear;
}

if ($filterMonth) {
    $whereConditions[] = "ps.bulan = ?";
    $params[] = $filterMonth;
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get payment history with filters
$query = "
    SELECT ps.*, s.nis, s.nama as nama_siswa, ta.nama as tahun_ajaran
    FROM pembayaran_spp ps
    JOIN siswa s ON ps.id_siswa = s.id
    JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id
    $whereClause
    ORDER BY ps.tanggal_bayar DESC, s.nis ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$payments = $stmt->fetchAll();

// Calculate summary
$totalPayments = count($payments);
$totalAmount = array_sum(array_column($payments, 'jumlah_bayar'));

ob_start();
?>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-primary-800">Riwayat Cicilan SPP</h1>
        <a href="spp-students" 
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="solar:arrow-left-linear" width="20" height="20"></iconify-icon>
            Kembali ke Daftar Siswa
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-md border border-secondary-200 mb-6">
        <div class="px-6 py-4 border-b border-secondary-200">
            <h2 class="text-xl font-semibold text-secondary-800">Filter Riwayat</h2>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label for="student" class="block text-sm font-medium text-secondary-700 mb-2">Siswa</label>
                    <select name="student" id="student" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Siswa</option>
                        <?php foreach ($allStudents as $student): ?>
                            <option value="<?= $student['id'] ?>" <?= $student['id'] == $filterStudent ? 'selected' : '' ?>>
                                <?= htmlspecialchars($student['nis'] . ' - ' . $student['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="year" class="block text-sm font-medium text-secondary-700 mb-2">Tahun Ajaran</label>
                    <select name="year" id="year" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($allYears as $year): ?>
                            <option value="<?= $year['id'] ?>" <?= $year['id'] == $filterYear ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="month" class="block text-sm font-medium text-secondary-700 mb-2">Bulan</label>
                    <select name="month" id="month" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Bulan</option>
                        <?php foreach ($months as $month): ?>
                            <option value="<?= htmlspecialchars($month) ?>" <?= $month === $filterMonth ? 'selected' : '' ?>>
                                <?= htmlspecialchars($month) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                        <iconify-icon icon="solar:magnifer-linear" width="20" height="20"></iconify-icon>
                        Filter
                    </button>
                </div>
                
                <?php if ($filterStudent || $filterYear || $filterMonth): ?>
                    <div class="col-span-full">
                        <a href="spp-history" 
                           class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition text-sm">
                            <iconify-icon icon="solar:close-circle-linear" width="20" height="20"></iconify-icon>
                            Reset Filter
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md border border-secondary-200">
            <div class="p-6 text-center">
                <h3 class="text-lg font-semibold text-secondary-700 mb-2">Total Transaksi</h3>
                <div class="text-3xl font-bold text-primary-600"><?= number_format($totalPayments, 0, ',', '.') ?></div>
                <p class="text-sm text-secondary-500 mt-2">cicilan pembayaran</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md border border-secondary-200">
            <div class="p-6 text-center">
                <h3 class="text-lg font-semibold text-secondary-700 mb-2">Total Pembayaran</h3>
                <div class="text-3xl font-bold text-status-success-600">Rp <?= number_format($totalAmount, 0, ',', '.') ?></div>
                <p class="text-sm text-secondary-500 mt-2">total nilai pembayaran</p>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="bg-white rounded-lg shadow-md border border-secondary-200">
        <div class="px-6 py-4 border-b border-secondary-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="text-xl font-semibold text-secondary-800">Riwayat Pembayaran</h2>
            <?php if (!empty($payments)): ?>
                <a href="spp-history-export.php?student=<?= $filterStudent ?>&year=<?= $filterYear ?>&month=<?= urlencode($filterMonth) ?>"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-accent-500 text-white hover:bg-accent-600 transition">
                    <iconify-icon icon="solar:download-linear" width="20" height="20"></iconify-icon>
                    Export Excel
                </a>
            <?php endif; ?>
        </div>
        <div class="p-6">
            <?php if (empty($payments)): ?>
                <div class="bg-accent-100 border border-accent-200 text-accent-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <iconify-icon icon="solar:info-circle-bold" class="mr-2 text-lg"></iconify-icon>
                        Tidak ada data pembayaran ditemukan dengan filter yang dipilih.
                    </div>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-primary-100 text-primary-700">
                            <tr>
                                <th class="px-4 py-2 font-semibold">No</th>
                                <th class="px-4 py-2 font-semibold">Tanggal Bayar</th>
                                <th class="px-4 py-2 font-semibold">NIS</th>
                                <th class="px-4 py-2 font-semibold">Nama Siswa</th>
                                <th class="px-4 py-2 font-semibold">Tahun Ajaran</th>
                                <th class="px-4 py-2 font-semibold">Bulan</th>
                                <th class="px-4 py-2 font-semibold">Jumlah Bayar</th>
                                <th class="px-4 py-2 font-semibold">ID Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-secondary-200">
                            <?php foreach ($payments as $index => $payment): ?>
                                <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= $index + 1 ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['nis'] ?? '-') ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['nama_siswa']) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['tahun_ajaran']) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($payment['bulan']) ?></td>
                                    <td class="px-4 py-3 text-right font-medium text-secondary-900 align-middle">
                                        Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?>
                                    </td>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function exportData() {
    alert('Export data ke Excel (fitur export akan dikembangkan)');
}
</script>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>