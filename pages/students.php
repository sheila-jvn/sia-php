<?php
$pageTitle = "Daftar Siswa";
$currentPage = 'students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();
$stmt = $pdo->query('SELECT * FROM siswa ORDER BY nama');
$students = $stmt->fetchAll();

ob_start();
?>
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
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
