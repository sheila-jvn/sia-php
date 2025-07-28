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
<main class="container py-4">
    <h1 class="mb-4">Daftar Siswa</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>NIS</th>
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>No KK</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Nama Ayah</th>
                    <th>Nama Ibu</th>
                    <th>NIK Ayah</th>
                    <th>NIK Ibu</th>
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
                        <td><?= htmlspecialchars($siswa['no_kk']) ?></td>
                        <td><?= htmlspecialchars($siswa['tanggal_lahir']) ?></td>
                        <td><?= $siswa['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td><?= htmlspecialchars($siswa['nama_ayah']) ?></td>
                        <td><?= htmlspecialchars($siswa['nama_ibu']) ?></td>
                        <td><?= htmlspecialchars($siswa['nik_ayah']) ?></td>
                        <td><?= htmlspecialchars($siswa['nik_ibu']) ?></td>
                        <td><?= htmlspecialchars($siswa['alamat']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/_layout.php';
