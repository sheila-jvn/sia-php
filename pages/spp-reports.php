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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Laporan & Export SPP</h2>
                <a href="spp-students" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3><?= number_format($totalStudents, 0, ',', '.') ?></h3>
                            <p class="mb-0">Total Siswa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5><?= number_format($thisMonth['total'] ?? 0, 0, ',', '.') ?> Transaksi</h5>
                            <h6>Rp <?= number_format($thisMonth['amount'] ?? 0, 0, ',', '.') ?></h6>
                            <p class="mb-0">Bulan Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5><?= number_format($thisYear['total'] ?? 0, 0, ',', '.') ?> Transaksi</h5>
                            <h6>Rp <?= number_format($thisYear['amount'] ?? 0, 0, ',', '.') ?></h6>
                            <p class="mb-0">Tahun Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h3>-</h3>
                            <p class="mb-0">Belum Lunas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Export Data Summary</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Export ringkasan pembayaran SPP per siswa per bulan</p>
                            <div class="d-grid gap-2">
                                <button onclick="exportSummary()" class="btn btn-success">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Download Summary CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Export Data Detail</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Export semua transaksi pembayaran SPP secara detail</p>
                            <div class="d-grid gap-2">
                                <button onclick="exportDetail()" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-text"></i> Download Detail CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="spp-students" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-people"></i><br>
                                Daftar Siswa
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="spp-history" class="btn btn-outline-info w-100 mb-2">
                                <i class="bi bi-clock-history"></i><br>
                                Riwayat Cicilan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button onclick="printMonthlyReport()" class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-printer"></i><br>
                                Laporan Bulanan
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button onclick="printYearlyReport()" class="btn btn-outline-warning w-100 mb-2">
                                <i class="bi bi-file-text"></i><br>
                                Laporan Tahunan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pembayaran Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentPayments)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Belum ada data pembayaran.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Bulan</th>
                                        <th>Jumlah</th>
                                        <th>ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPayments as $payment): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></td>
                                            <td><?= htmlspecialchars($payment['nis'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($payment['nama_siswa']) ?></td>
                                            <td><?= htmlspecialchars($payment['tahun_ajaran']) ?></td>
                                            <td><?= htmlspecialchars($payment['bulan']) ?></td>
                                            <td class="text-end">Rp <?= number_format($payment['jumlah_bayar'], 0, ',', '.') ?></td>
                                            <td><code>#<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></code></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="spp-history" class="btn btn-outline-primary">
                                Lihat Semua Riwayat
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportSummary() {
    alert('Export summary CSV (fitur export akan dikembangkan)');
}

function exportDetail() {
    alert('Export detail CSV (fitur export akan dikembangkan)');
}

function printMonthlyReport() {
    const month = new Date().toLocaleString('id-ID', { month: 'long' });
    const year = new Date().getFullYear();
    alert('Print laporan bulanan untuk ' + month + ' ' + year + ' (fitur print akan dikembangkan)');
}

function printYearlyReport() {
    const year = new Date().getFullYear();
    alert('Print laporan tahunan untuk tahun ' + year + ' (fitur print akan dikembangkan)');
}
</script>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>