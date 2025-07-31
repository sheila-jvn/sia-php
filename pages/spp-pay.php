<?php
$pageTitle = "Bayar SPP";
$currentPage = 'spp-students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Get parameters
$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$yearId = isset($_GET['year_id']) ? (int)$_GET['year_id'] : 0;
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';

if (!$studentId) {
    header("Location: spp-students");
    exit();
}

// Fixed SPP amount per month
$sppAmount = 650000;

// Define months in order
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$success = '';
$error = '';
$allocation = [];

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

// Process payment if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $startMonth = $_POST['start_month'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        
        if (!$startMonth || $amount <= 0) {
            throw new Exception("Mohon lengkapi semua field dengan benar");
        }
        
        if (!in_array($startMonth, $months)) {
            throw new Exception("Bulan tidak valid");
        }
        
        // Get current payments for this student and year
        $stmt = $pdo->prepare("
            SELECT bulan, SUM(jumlah_bayar) as total_paid 
            FROM pembayaran_spp 
            WHERE id_siswa = ? AND id_tahun_ajaran = ? 
            GROUP BY bulan
        ");
        $stmt->execute([$studentId, $yearId]);
        $currentPayments = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Calculate allocation
        $remainingAmount = $amount;
        $allocation = [];
        $startIndex = array_search($startMonth, $months);
        
        for ($i = $startIndex; $i < count($months) && $remainingAmount > 0; $i++) {
            $month = $months[$i];
            $alreadyPaid = $currentPayments[$month] ?? 0;
            $outstanding = max(0, $sppAmount - $alreadyPaid);
            
            if ($outstanding > 0) {
                $allocatedAmount = min($remainingAmount, $outstanding);
                $allocation[] = [
                    'month' => $month,
                    'amount' => $allocatedAmount,
                    'already_paid' => $alreadyPaid,
                    'new_total' => $alreadyPaid + $allocatedAmount,
                    'status' => ($alreadyPaid + $allocatedAmount >= $sppAmount) ? 'Lunas' : 'Belum Lunas'
                ];
                $remainingAmount -= $allocatedAmount;
            }
        }
        
        if ($remainingAmount > 0) {
            throw new Exception("Pembayaran melebihi total SPP yang belum dibayar. Kelebihan: Rp " . number_format($remainingAmount, 0, ',', '.'));
        }
        
        if (empty($allocation)) {
            throw new Exception("Tidak ada pembayaran yang dapat dialokasikan");
        }
        
        // If this is confirmation step, save to database
        if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
            $pdo->beginTransaction();
            
            try {
                foreach ($allocation as $alloc) {
                    $stmt = $pdo->prepare("
                        INSERT INTO pembayaran_spp (id_siswa, id_tahun_ajaran, bulan, tanggal_bayar, jumlah_bayar) 
                        VALUES (?, ?, ?, CURDATE(), ?)
                    ");
                    $stmt->execute([$studentId, $yearId, $alloc['month'], $alloc['amount']]);
                }
                
                $pdo->commit();
                $success = "Pembayaran berhasil disimpan!";
                
                // Clear allocation for display
                $showAllocation = $allocation;
                $allocation = [];
                
            } catch (Exception $e) {
                $pdo->rollback();
                throw new Exception("Gagal menyimpan pembayaran: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-primary-800 mb-2">Bayar SPP</h1>
            <h2 class="text-xl text-secondary-600 mb-1"><?= htmlspecialchars($student['nama']) ?> (NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?>)</h2>
            <p class="text-sm text-secondary-500">Tahun Ajaran: <?= htmlspecialchars($year['nama']) ?></p>
        </div>
        <a href="spp-status?id=<?= $studentId ?>&year=<?= $yearId ?>" 
           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
            <iconify-icon icon="solar:arrow-left-linear" width="20" height="20"></iconify-icon>
            Kembali
        </a>
    </div>

    <?php if ($error): ?>
        <div class="bg-status-error-100 border border-status-error-200 text-status-error-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <iconify-icon icon="solar:danger-triangle-bold" class="mr-2 text-lg"></iconify-icon>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-status-success-100 border border-status-success-200 text-status-success-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-3">
                <iconify-icon icon="solar:check-circle-bold" class="mr-2 text-lg"></iconify-icon>
                <?= htmlspecialchars($success) ?>
            </div>
            
            <?php if (isset($showAllocation)): ?>
                <hr class="border-status-success-200 my-4">
                <h3 class="text-lg font-semibold mb-3">Detail Alokasi Pembayaran:</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-secondary-200 rounded-lg">
                        <thead class="bg-secondary-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Bulan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Jumlah Dibayar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Total Setelah Bayar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-200">
                            <?php foreach ($showAllocation as $alloc): ?>
                                <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($alloc['month']) ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle">Rp <?= number_format($alloc['amount'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle">Rp <?= number_format($alloc['new_total'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3 align-middle">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $alloc['status'] === 'Lunas' ? 'bg-status-success-100 text-status-success-700' : 'bg-status-warning-100 text-status-warning-700' ?>">
                                            <?= $alloc['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-md border border-secondary-200">
            <div class="px-6 py-4 border-b border-secondary-200">
                <h2 class="text-xl font-semibold text-secondary-800">Form Pembayaran SPP</h2>
                <p class="text-sm text-secondary-600 mt-1">SPP per bulan: Rp <?= number_format($sppAmount, 0, ',', '.') ?></p>
            </div>
            <div class="p-6">
                <?php if (empty($allocation)): ?>
                    <!-- Initial Form -->
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_month" class="block text-sm font-medium text-secondary-700 mb-2">Mulai dari Bulan</label>
                            <select name="start_month" id="start_month" 
                                    class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                    required>
                                <option value="">Pilih Bulan</option>
                                <?php foreach ($months as $month): ?>
                                    <option value="<?= htmlspecialchars($month) ?>" 
                                            <?= $month === $selectedMonth ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($month) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-secondary-500 mt-1">Pembayaran akan dialokasikan mulai dari bulan ini</p>
                        </div>
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium text-secondary-700 mb-2">Jumlah Pembayaran</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-secondary-500">Rp</span>
                                <input type="number" name="amount" id="amount" 
                                       class="w-full pl-8 pr-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                       required>
                            </div>
                            <p class="text-xs text-secondary-500 mt-1">
                                Dapat lebih dari Rp <?= number_format($sppAmount, 0, ',', '.') ?> untuk pembayaran beberapa bulan sekaligus
                            </p>
                        </div>
                        
                        <div class="col-span-full">
                            <button type="submit" 
                                    class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                                <iconify-icon icon="solar:calculator-minimalistic-linear" width="20" height="20"></iconify-icon>
                                Hitung Alokasi
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Allocation Preview -->
                    <div class="bg-accent-100 border border-accent-200 text-accent-700 px-4 py-3 rounded-lg mb-6">
                        <h3 class="flex items-center text-lg font-semibold mb-2">
                            <iconify-icon icon="solar:info-circle-bold" class="mr-2"></iconify-icon>
                            Preview Alokasi Pembayaran
                        </h3>
                        <p>Berikut adalah alokasi pembayaran yang akan dilakukan:</p>
                    </div>
                    
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full bg-white border border-secondary-200 rounded-lg">
                            <thead class="bg-secondary-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Bulan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Sudah Dibayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Jumlah Cicilan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Total Setelah Bayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-secondary-700 uppercase tracking-wider border-b border-secondary-200">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-200">
                                <?php foreach ($allocation as $alloc): ?>
                                    <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                                        <td class="px-4 py-3 font-medium text-secondary-900 align-middle"><?= htmlspecialchars($alloc['month']) ?></td>
                                        <td class="px-4 py-3 text-secondary-700 align-middle">Rp <?= number_format($alloc['already_paid'], 0, ',', '.') ?></td>
                                        <td class="px-4 py-3 text-status-success-700 font-medium align-middle">
                                            + Rp <?= number_format($alloc['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3 font-medium text-secondary-900 align-middle">Rp <?= number_format($alloc['new_total'], 0, ',', '.') ?></td>
                                        <td class="px-4 py-3 align-middle">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?= $alloc['status'] === 'Lunas' ? 'bg-status-success-100 text-status-success-700' : 'bg-status-warning-100 text-status-warning-700' ?>">
                                                <?= $alloc['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-secondary-50">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-sm font-medium text-secondary-700 border-t border-secondary-200">Total Pembayaran:</th>
                                    <th colspan="3" class="px-4 py-3 text-sm font-medium text-secondary-900 border-t border-secondary-200">
                                        Rp <?= number_format(array_sum(array_column($allocation, 'amount')), 0, ',', '.') ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <form method="POST" class="flex flex-col sm:flex-row gap-3">
                        <input type="hidden" name="start_month" value="<?= htmlspecialchars($_POST['start_month']) ?>">
                        <input type="hidden" name="amount" value="<?= htmlspecialchars($_POST['amount']) ?>">
                        <input type="hidden" name="confirm" value="yes">
                        
                        <button type="submit" 
                                class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-status-success-600 text-white hover:bg-status-success-700 transition">
                            <iconify-icon icon="solar:check-circle-bold" width="20" height="20"></iconify-icon>
                            Konfirmasi Pembayaran
                        </button>
                        <a href="spp-pay?student_id=<?= $studentId ?>&year_id=<?= $yearId ?><?= $selectedMonth ? '&month=' . urlencode($selectedMonth) : '' ?>" 
                           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-100 transition">
                            <iconify-icon icon="solar:restart-linear" width="20" height="20"></iconify-icon>
                            Ubah Jumlah
                        </a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Format number input
document.getElementById('amount')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^\d]/g, '');
    e.target.value = value;
});
</script>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>