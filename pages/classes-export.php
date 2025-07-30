<?php
require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';

$sql = 'SELECT 
            k.id,
            k.nama AS nama_kelas,
            ta.nama AS nama_tahun_ajaran,
            t.nama AS nama_tingkat,
            g.nama AS nama_guru_wali,
            g.nip AS nip_guru_wali
        FROM kelas k 
        JOIN tahun_ajaran ta ON k.id_tahun_ajaran = ta.id 
        JOIN tingkat t ON k.id_tingkat = t.id 
        LEFT JOIN guru g ON k.id_guru_wali = g.id';

$params = [];

if ($searchQuery) {
    $sql .= ' WHERE k.nama LIKE :search_nama 
              OR ta.nama LIKE :search_tahun 
              OR t.nama LIKE :search_tingkat 
              OR g.nama LIKE :search_guru';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_tahun'] = '%' . $searchQuery . '%';
    $params[':search_tingkat'] = '%' . $searchQuery . '%';
    $params[':search_guru'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY k.nama';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$classes = $stmt->fetchAll();

$timestamp = date('Y-m-d_H-i-s');
$filename = "data_kelas_" . $timestamp . ".csv";

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID',
    'Nama Kelas',
    'Tahun Ajaran',
    'Tingkat',
    'Guru Wali Kelas',
    'NIP Guru Wali'
]);

foreach ($classes as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nama_kelas'],
        $row['nama_tahun_ajaran'],
        $row['nama_tingkat'],
        $row['nama_guru_wali'] ?: 'Belum ditentukan',
        $row['nip_guru_wali'] ?: '-'
    ]);
}

fclose($output);
exit;