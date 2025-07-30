<?php
require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

$searchQuery = $_GET['search'] ?? '';

$sql = 'SELECT 
            n.id,
            s.nama AS nama_siswa,
            s.nis AS nis_siswa,
            mp.nama AS nama_mata_pelajaran,
            k.nama AS nama_kelas,
            ta.nama AS nama_tahun_ajaran,
            nj.nama AS jenis_nilai,
            n.nilai,
            n.tanggal_penilaian,
            n.keterangan
        FROM nilai n
        JOIN siswa s ON n.id_siswa = s.id
        JOIN mata_pelajaran mp ON n.id_mata_pelajaran = mp.id
        JOIN kelas k ON n.id_kelas = k.id
        JOIN tahun_ajaran ta ON n.id_tahun_ajaran = ta.id
        JOIN nilai_jenis nj ON n.id_jenis_nilai = nj.id';

$params = [];

if ($searchQuery) {
    $sql .= ' WHERE s.nama LIKE :search_siswa 
              OR s.nis LIKE :search_nis
              OR mp.nama LIKE :search_mapel 
              OR k.nama LIKE :search_kelas 
              OR ta.nama LIKE :search_tahun
              OR nj.nama LIKE :search_jenis
              OR n.keterangan LIKE :search_keterangan';
    $params[':search_siswa'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_mapel'] = '%' . $searchQuery . '%';
    $params[':search_kelas'] = '%' . $searchQuery . '%';
    $params[':search_tahun'] = '%' . $searchQuery . '%';
    $params[':search_jenis'] = '%' . $searchQuery . '%';
    $params[':search_keterangan'] = '%' . $searchQuery . '%';
}

$sql .= ' ORDER BY n.tanggal_penilaian DESC, s.nama ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$nilaiList = $stmt->fetchAll();

$timestamp = date('Y-m-d_H-i-s');
$filename = "data_nilai_" . $timestamp . ".csv";

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
    'Mata Pelajaran',
    'Kelas',
    'Tahun Ajaran',
    'Jenis Nilai',
    'Nilai',
    'Tanggal Penilaian',
    'Keterangan'
]);

foreach ($nilaiList as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nama_siswa'],
        $row['nis_siswa'] ?: '-',
        $row['nama_mata_pelajaran'],
        $row['nama_kelas'],
        $row['nama_tahun_ajaran'],
        $row['jenis_nilai'],
        $row['nilai'],
        date('d/m/Y', strtotime($row['tanggal_penilaian'])),
        $row['keterangan'] ?: '-'
    ]);
}

fclose($output);
exit;