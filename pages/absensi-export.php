<?php
require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';

$sql = 'SELECT 
            kh.id,
            s.nama AS nama_siswa,
            s.nis AS nis_siswa,
            k.nama AS nama_kelas,
            ta.nama AS nama_tahun_ajaran,
            ks.nama AS status_kehadiran,
            kh.tanggal,
            kh.keterangan
        FROM kehadiran kh
        JOIN siswa s ON kh.id_siswa = s.id
        JOIN kelas k ON kh.id_kelas = k.id
        JOIN tahun_ajaran ta ON kh.id_tahun_ajaran = ta.id
        JOIN kehadiran_status ks ON kh.id_status = ks.id';

$params = [];

if ($searchQuery) {
    $sql .= ' WHERE s.nama LIKE :search_siswa 
              OR s.nis LIKE :search_nis
              OR k.nama LIKE :search_kelas 
              OR ta.nama LIKE :search_tahun
              OR ks.nama LIKE :search_status
              OR kh.keterangan LIKE :search_keterangan';
    $params[':search_siswa'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_kelas'] = '%' . $searchQuery . '%';
    $params[':search_tahun'] = '%' . $searchQuery . '%';
    $params[':search_status'] = '%' . $searchQuery . '%';
    $params[':search_keterangan'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY kh.tanggal DESC, s.nama ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$absensiList = $stmt->fetchAll();

$timestamp = date('Y-m-d_H-i-s');
$filename = "data_absensi_" . $timestamp . ".csv";

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID',
    'Nama Siswa',
    'NIS',
    'Kelas',
    'Tahun Ajaran',
    'Status Kehadiran',
    'Tanggal',
    'Keterangan'
]);

foreach ($absensiList as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nama_siswa'],
        $row['nis_siswa'] ?: '-',
        $row['nama_kelas'],
        $row['nama_tahun_ajaran'],
        $row['status_kehadiran'],
        date('d/m/Y', strtotime($row['tanggal'])),
        $row['keterangan'] ?: '-'
    ]);
}

fclose($output);
exit;