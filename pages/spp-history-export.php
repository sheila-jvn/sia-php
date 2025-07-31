<?php
require_once __DIR__ . '/../lib/database.php';

$filterStudent = isset($_GET['student']) ? (int)$_GET['student'] : 0;
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$filterMonth = isset($_GET['month']) ? $_GET['month'] : '';

// Generate descriptive filename
$filename = 'riwayat_spp_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$pdo = getDbConnection();

// Build filter conditions (same logic as main page)
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

// Start CSV output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for better Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV headers
fputcsv($output, [
    'No',
    'ID Transaksi', 
    'Tanggal Bayar', 
    'NIS', 
    'Nama Siswa', 
    'Tahun Ajaran', 
    'Bulan', 
    'Jumlah Bayar'
]);

// Export data rows
foreach ($payments as $index => $row) {
    fputcsv($output, [
        $index + 1,
        str_pad($row['id'], 6, '0', STR_PAD_LEFT),
        date('d/m/Y', strtotime($row['tanggal_bayar'])),
        $row['nis'],
        $row['nama_siswa'],
        $row['tahun_ajaran'],
        $row['bulan'],
        number_format($row['jumlah_bayar'], 0, ',', '.')
    ]);
}

fclose($output);
exit;
