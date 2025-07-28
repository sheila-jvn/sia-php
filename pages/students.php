<?php
// pages/students.php
$pageTitle = "Daftar Siswa";

require_once __DIR__ . '/../lib/database.php';

// Fetch all students
$pdo = getDbConnection();
$stmt = $pdo->query('SELECT * FROM siswa ORDER BY nama');
$students = $stmt->fetchAll();

ob_start();
?>
<div class="d-flex">
    <div class="flex-shrink-0 p-3 bg-light" style="width: 220px; min-height: 100vh;">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <span class="fs-4">SIA</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard" class="nav-link link-dark">Dashboard</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="nav-link active">Students</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/logout" class="nav-link link-dark">Logout</a>
            </li>
        </ul>
    </div>
    <div class="flex-grow-1 p-4" style="min-width:0; overflow-x:auto;">
        <h1 class="mb-4">Daftar Siswa</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $siswa): ?>
                        <tr>
                            <td><?= htmlspecialchars($siswa['id']) ?></td>
                            <td><?= htmlspecialchars($siswa['nis']) ?></td>
                            <td><?= htmlspecialchars($siswa['nisn']) ?></td>
                            <td><?= htmlspecialchars($siswa['nama']) ?></td>
                            <td><?= htmlspecialchars($siswa['tanggal_lahir']) ?></td>
                            <td><?= $siswa['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td><?= htmlspecialchars($siswa['alamat']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/_layout.php';
