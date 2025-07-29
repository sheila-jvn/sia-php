<?php
require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';
$sql = 'SELECT * FROM guru';
$params = [];

if ($searchQuery) {
    $sql .= ' WHERE nama LIKE :search_nama OR nip LIKE :search_nip OR no_telpon LIKE :search_no_telpon';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_nip'] = '%' . $searchQuery . '%';
    $params[':search_no_telpon'] = '%' . $searchQuery . '%';
}
$sql .= ' ORDER BY nama';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$teachers = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="data_guru.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// CSV header
fputcsv($output, [
    'ID', 'NIP', 'Nama', 'Tanggal Lahir', 'Jenis Kelamin', 'No. Telepon'
]);

foreach ($teachers as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nip'],
        $row['nama'],
        $row['tanggal_lahir'],
        $row['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan',
        $row['no_telpon'],
    ]);
}
fclose($output);
exit;
