<?php
require_once __DIR__ . '/../lib/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="spp_detail.csv"');

$pdo = getDbConnection();

// Get all payment transactions with student and year info
$stmt = $pdo->query('SELECT ps.id, s.nis, s.nama as nama_siswa, ta.nama as tahun_ajaran, ps.bulan, ps.tanggal_bayar, ps.jumlah_bayar FROM pembayaran_spp ps JOIN siswa s ON ps.id_siswa = s.id JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id ORDER BY ps.tanggal_bayar DESC, ps.id DESC');
$rows = $stmt->fetchAll();

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'NIS', 'Nama Siswa', 'Tahun Ajaran', 'Bulan', 'Tanggal Bayar', 'Jumlah Bayar']);
foreach ($rows as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nis'],
        $row['nama_siswa'],
        $row['tahun_ajaran'],
        $row['bulan'],
        $row['tanggal_bayar'],
        $row['jumlah_bayar']
    ]);
}
fclose($output);
exit;
