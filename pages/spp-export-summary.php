<?php
require_once __DIR__ . '/../lib/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="spp_summary.csv"');

$pdo = getDbConnection();

// Get all students
$students = $pdo->query('SELECT id, nis, nama FROM siswa ORDER BY nis ASC')->fetchAll();
// Get all academic years
$years = $pdo->query('SELECT id, nama FROM tahun_ajaran ORDER BY tahun_mulai DESC')->fetchAll();
// Define months
$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$output = fopen('php://output', 'w');
// Header row
fputcsv($output, ['NIS', 'Nama Siswa', 'Tahun Ajaran', 'Bulan', 'Total Dibayar', 'Status']);

foreach ($students as $student) {
    foreach ($years as $year) {
        foreach ($months as $month) {
            $stmt = $pdo->prepare('SELECT SUM(jumlah_bayar) as total FROM pembayaran_spp WHERE id_siswa = ? AND id_tahun_ajaran = ? AND bulan = ?');
            $stmt->execute([$student['id'], $year['id'], $month]);
            $total = $stmt->fetchColumn() ?: 0;
            $status = ($total >= 650000) ? 'Lunas' : 'Belum Lunas';
            fputcsv($output, [
                $student['nis'],
                $student['nama'],
                $year['nama'],
                $month,
                $total,
                $status
            ]);
        }
    }
}
fclose($output);
exit;
