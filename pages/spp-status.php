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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Status Pembayaran SPP</h2>
                    <h5 class="text-muted"><?= htmlspecialchars($student['nama']) ?> (NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?>)</h5>
                </div>
                <a href="spp-students" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Academic Year Selection -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row align-items-end">
                        <input type="hidden" name="id" value="<?= $studentId ?>">
                        <div class="col-md-4">
                            <label for="year" class="form-label">Tahun Ajaran</label>
                            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Pembayaran SPP - <?= htmlspecialchars($year['nama']) ?></h5>
                    <small class="text-muted">SPP per bulan: Rp <?= number_format($sppAmount, 0, ',', '.') ?></small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Riwayat Cicilan</th>
                                    <th>Total Dibayar</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($months as $month): 
                                    $data = $monthlyData[$month];
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($month) ?></strong></td>
                                        <td>
                                            <?php if (empty($data['payments'])): ?>
                                                <span class="text-muted">Belum ada pembayaran</span>
                                            <?php else: ?>
                                                <div class="small">
                                                    <?php foreach ($data['payments'] as $payment): ?>
                                                        <div>
                                                            <?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?>: 
                                                            <strong>Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?></strong>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong>Rp <?= number_format($data['total_paid'], 0, ',', '.') ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($data['outstanding'] > 0): ?>
                                                <span class="text-danger">
                                                    Rp <?= number_format($data['outstanding'], 0, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-success">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($data['status'] === 'Lunas'): ?>
                                                <span class="badge bg-success">Lunas</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Belum Lunas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($data['outstanding'] > 0): ?>
                                                <a href="spp-pay?student_id=<?= $studentId ?>&year_id=<?= $yearId ?>&month=<?= urlencode($month) ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="bi bi-plus-circle"></i> Bayar Cicil
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <i class="bi bi-check-circle"></i> Lunas
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($data['payments'])): ?>
                                                <button class="btn btn-outline-info btn-sm ms-1" 
                                                        onclick="printReceipt('<?= $month ?>')">
                                                    <i class="bi bi-printer"></i> Print
                                                </button>
                                            <?php endif; ?>
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
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Dibayar</h5>
                                    <h4 class="text-success">Rp <?= number_format($totalPaidAllMonths, 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Sisa</h5>
                                    <h4 class="text-danger">Rp <?= number_format($totalOutstandingAllMonths, 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Bulan Lunas</h5>
                                    <h4 class="text-info"><?= $lunasBulan ?> / 12</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Progress</h5>
                                    <h4 class="text-primary"><?= number_format(($lunasBulan / 12) * 100, 1) ?>%</h4>
                                </div>
                            </div>
                        </div>
                    </div>
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