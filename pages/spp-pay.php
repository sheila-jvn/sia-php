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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Bayar SPP</h2>
                    <h5 class="text-muted"><?= htmlspecialchars($student['nama']) ?> (NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?>)</h5>
                    <small class="text-muted">Tahun Ajaran: <?= htmlspecialchars($year['nama']) ?></small>
                </div>
                <a href="spp-status?id=<?= $studentId ?>&year=<?= $yearId ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success) ?>
                    
                    <?php if (isset($showAllocation)): ?>
                        <hr>
                        <h6>Detail Alokasi Pembayaran:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Jumlah Dibayar</th>
                                        <th>Total Setelah Bayar</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($showAllocation as $alloc): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($alloc['month']) ?></td>
                                            <td>Rp <?= number_format($alloc['amount'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($alloc['new_total'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $alloc['status'] === 'Lunas' ? 'success' : 'warning' ?>">
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Pembayaran SPP</h5>
                        <small class="text-muted">SPP per bulan: Rp <?= number_format($sppAmount, 0, ',', '.') ?></small>
                    </div>
                    <div class="card-body">
                        <?php if (empty($allocation)): ?>
                            <!-- Initial Form -->
                            <form method="POST" class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_month" class="form-label">Mulai dari Bulan</label>
                                    <select name="start_month" id="start_month" class="form-select" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php foreach ($months as $month): ?>
                                            <option value="<?= htmlspecialchars($month) ?>" 
                                                    <?= $month === $selectedMonth ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($month) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Pembayaran akan dialokasikan mulai dari bulan ini</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Jumlah Pembayaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="amount" id="amount" class="form-control" 
                                                required>
                                    </div>
                                    <div class="form-text">
                                        Dapat lebih dari Rp <?= number_format($sppAmount, 0, ',', '.') ?> untuk pembayaran beberapa bulan sekaligus
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-calculator"></i> Hitung Alokasi
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- Allocation Preview -->
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle"></i> Preview Alokasi Pembayaran</h6>
                                <p>Berikut adalah alokasi pembayaran yang akan dilakukan:</p>
                            </div>
                            
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Sudah Dibayar</th>
                                            <th>Jumlah Cicilan</th>
                                            <th>Total Setelah Bayar</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allocation as $alloc): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($alloc['month']) ?></strong></td>
                                                <td>Rp <?= number_format($alloc['already_paid'], 0, ',', '.') ?></td>
                                                <td>
                                                    <span class="text-success">
                                                        + Rp <?= number_format($alloc['amount'], 0, ',', '.') ?>
                                                    </span>
                                                </td>
                                                <td><strong>Rp <?= number_format($alloc['new_total'], 0, ',', '.') ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $alloc['status'] === 'Lunas' ? 'success' : 'warning' ?>">
                                                        <?= $alloc['status'] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total Pembayaran:</th>
                                            <th colspan="3">
                                                Rp <?= number_format(array_sum(array_column($allocation, 'amount')), 0, ',', '.') ?>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="start_month" value="<?= htmlspecialchars($_POST['start_month']) ?>">
                                <input type="hidden" name="amount" value="<?= htmlspecialchars($_POST['amount']) ?>">
                                <input type="hidden" name="confirm" value="yes">
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Konfirmasi Pembayaran
                                </button>
                                <a href="spp-pay?student_id=<?= $studentId ?>&year_id=<?= $yearId ?><?= $selectedMonth ? '&month=' . urlencode($selectedMonth) : '' ?>" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Ubah Jumlah
                                </a>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
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