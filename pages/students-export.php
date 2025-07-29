<?php
require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';
$sql = 'SELECT * FROM siswa';
$params = [];

if ($searchQuery) {
    $sql .= ' WHERE nama LIKE :search_nama OR nis LIKE :search_nis OR nisn LIKE :search_nisn OR alamat LIKE :search_alamat';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_nisn'] = '%' . $searchQuery . '%';
    $params[':search_alamat'] = '%' . $searchQuery . '%';
}
$sql .= ' ORDER BY nama';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="data_siswa.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// CSV header
fputcsv($output, [
    'ID', 'NIS', 'NISN', 'Nama', 'Nomor KK', 'Tanggal Lahir', 'Jenis Kelamin', 'Alamat', 'Nama Ayah', 'NIK Ayah', 'Nama Ibu', 'NIK Ibu'
]);

foreach ($students as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nis'],
        $row['nisn'],
        $row['nama'],
        $row['no_kk'],
        $row['tanggal_lahir'],
        $row['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan',
        $row['alamat'],
        $row['nama_ayah'],
        $row['nik_ayah'],
        $row['nama_ibu'],
        $row['nik_ibu'],
    ]);
}
fclose($output);
exit;
