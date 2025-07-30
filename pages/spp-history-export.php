<?php
require_once __DIR__ . '/../lib/database.php';

$filterStudent = isset($_GET['student']) ? (int)$_GET['student'] : 0;
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$filterMonth = isset($_GET['month']) ? $_GET['month'] : '';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="spp_history.csv"');

$pdo = getDbConnection();
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

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Tanggal Bayar', 'NIS', 'Nama Siswa', 'Tahun Ajaran', 'Bulan', 'Jumlah Bayar']);
foreach ($payments as $row) {
    fputcsv($output, [
        $row['id'],
        $row['tanggal_bayar'],
        $row['nis'],
        $row['nama_siswa'],
        $row['tahun_ajaran'],
        $row['bulan'],
        $row['jumlah_bayar']
    ]);
}
fclose($output);
exit;
