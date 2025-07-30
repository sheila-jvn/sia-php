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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Riwayat Cicilan SPP</h2>
                <a href="spp-students" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa
                </a>
            </div>

            <!-- Filter Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Riwayat</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="student" class="form-label">Siswa</label>
                            <select name="student" id="student" class="form-select">
                                <option value="">Semua Siswa</option>
                                <?php foreach ($allStudents as $student): ?>
                                    <option value="<?= $student['id'] ?>" <?= $student['id'] == $filterStudent ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($student['nis'] . ' - ' . $student['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="year" class="form-label">Tahun Ajaran</label>
                            <select name="year" id="year" class="form-select">
                                <option value="">Semua Tahun</option>
                                <?php foreach ($allYears as $year): ?>
                                    <option value="<?= $year['id'] ?>" <?= $year['id'] == $filterYear ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($year['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="month" class="form-label">Bulan</label>
                            <select name="month" id="month" class="form-select">
                                <option value="">Semua Bulan</option>
                                <?php foreach ($months as $month): ?>
                                    <option value="<?= htmlspecialchars($month) ?>" <?= $month === $filterMonth ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($month) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                        
                        <?php if ($filterStudent || $filterYear || $filterMonth): ?>
                            <div class="col-md-12">
                                <a href="spp-history" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i> Reset Filter
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Transaksi</h5>
                            <h3 class="text-primary"><?= number_format($totalPayments, 0, ',', '.') ?></h3>
                            <small class="text-muted">cicilan pembayaran</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Pembayaran</h5>
                            <h3 class="text-success">Rp <?= number_format($totalAmount, 0, ',', '.') ?></h3>
                            <small class="text-muted">total nilai pembayaran</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Pembayaran</h5>
                    <?php if (!empty($payments)): ?>
                        <button onclick="exportData()" class="btn btn-success btn-sm">
                            <i class="bi bi-download"></i> Export Excel
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($payments)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Tidak ada data pembayaran ditemukan dengan filter yang dipilih.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Bayar</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Bulan</th>
                                        <th>Jumlah Bayar</th>
                                        <th>ID Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $index => $payment): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></td>
                                            <td><?= htmlspecialchars($payment['nis'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($payment['nama_siswa']) ?></td>
                                            <td><?= htmlspecialchars($payment['tahun_ajaran']) ?></td>
                                            <td><?= htmlspecialchars($payment['bulan']) ?></td>
                                            <td class="text-end">
                                                <strong>Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?></strong>
                                            </td>
                                            <td>
                                                <code>#<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></code>
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