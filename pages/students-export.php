<?php
require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Ambil query pencarian untuk mempertahankan konsistensi filtering dengan halaman siswa utama
// Jika pengguna sedang melihat hasil yang difilter, export akan berisi hanya hasil tersebut
$searchQuery = $_GET['search'] ?? '';

// Gunakan logika SQL yang sama seperti halaman siswa utama untuk konsistensi
$sql = 'SELECT * FROM siswa';
$params = [];

// Terapkan filter pencarian yang sama jika istilah pencarian diberikan
if ($searchQuery) {
    // Pencarian multi-field yang sama seperti di students.php
    $sql .= ' WHERE nama LIKE :search_nama OR nis LIKE :search_nis OR nisn LIKE :search_nisn OR alamat LIKE :search_alamat';
    $params[':search_nama'] = '%' . $searchQuery . '%';
    $params[':search_nis'] = '%' . $searchQuery . '%';
    $params[':search_nisn'] = '%' . $searchQuery . '%';
    $params[':search_alamat'] = '%' . $searchQuery . '%';
}

// Urutkan berdasarkan nama untuk export (berbeda dari halaman utama yang mengurutkan berdasarkan NIS)
// Urutan alfabetis lebih berguna untuk data yang diekspor
$sql .= ' ORDER BY nama';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Set header HTTP yang tepat untuk download file CSV
// Header ini memberitahu browser untuk mengunduh file alih-alih menampilkannya
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="data_siswa.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output UTF-8 BOM (Byte Order Mark) untuk kompatibilitas Excel
// Tanpa ini, Excel mungkin tidak menampilkan karakter Indonesia dengan benar
// BOM memberitahu Excel bahwa file ini menggunakan encoding UTF-8
echo "\xEF\xBB\xBF";

// Buka stream output untuk menulis data CSV
// php://output memungkinkan kita menulis langsung ke stream download browser
$output = fopen('php://output', 'w');

// Tulis baris header CSV dengan nama kolom yang deskriptif
// Ini akan muncul sebagai baris pertama di file yang diekspor
fputcsv($output, [
    'ID', 'NIS', 'NISN', 'Nama', 'Nomor KK', 'Tanggal Lahir', 'Jenis Kelamin', 'Alamat', 'Nama Ayah', 'NIK Ayah', 'Nama Ibu', 'NIK Ibu'
]);

// Export setiap record siswa sebagai baris CSV
foreach ($students as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nis'],
        $row['nisn'],
        $row['nama'],
        $row['no_kk'],
        $row['tanggal_lahir'],
        // Konversi kode jenis kelamin ke teks yang dapat dibaca untuk export
        // Logika konversi yang sama seperti halaman tampilan, tapi untuk format CSV
        $row['jenis_kelamin'] == '1' ? 'Laki-laki' : 'Perempuan',
        $row['alamat'],
        $row['nama_ayah'],
        $row['nik_ayah'],
        $row['nama_ibu'],
        $row['nik_ibu'],
    ]);
}

// Bersihkan dan paksa download
fclose($output);
exit; // Penting: cegah output tambahan yang bisa merusak file CSV
