<?php
$pageTitle = "Pembayaran SPP - Daftar Siswa";
$currentPage = 'spp-students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Get all students with their class information
$sql = "
    SELECT s.id, s.nis, s.nama, k.nama as kelas_nama, ta.nama as tahun_ajaran
    FROM siswa s
    LEFT JOIN kelas k ON s.id = (
        SELECT id_siswa FROM kehadiran 
        WHERE id_siswa = s.id AND id_kelas = k.id 
        LIMIT 1
    )
    LEFT JOIN tahun_ajaran ta ON k.id_tahun_ajaran = ta.id
    ORDER BY s.nis ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();

// Get current academic year for default selection
$stmt = $pdo->prepare("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC LIMIT 1");
$stmt->execute();
$currentYear = $stmt->fetch();

ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Pembayaran SPP - Daftar Siswa</h2>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Siswa</h5>
                    <?php if ($currentYear): ?>
                        <small class="text-muted">Tahun Ajaran: <?= htmlspecialchars($currentYear['nama']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($students)): ?>
                        <div class="alert alert-info">
                            Belum ada data siswa.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['nis'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($student['nama']) ?></td>
                                            <td><?= htmlspecialchars($student['kelas_nama'] ?? 'Belum ada kelas') ?></td>
                                            <td>
                                                <a href="spp-status?id=<?= $student['id'] ?><?= $currentYear ? '&year=' . $currentYear['id'] : '' ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="bi bi-cash-stack"></i> Lihat Pembayaran
                                                </a>
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

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>