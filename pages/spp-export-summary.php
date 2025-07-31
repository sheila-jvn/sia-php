<?php
require_once __DIR__ . '/../lib/database.php';

try {
    $pdo = getDbConnection();
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="spp_summary_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // Write BOM for UTF-8 to ensure proper encoding in Excel
    fwrite($output, "\xEF\xBB\xBF");
    
    // Header row
    fputcsv($output, [
        'ID Transaksi',
        'NIS', 
        'Nama Siswa', 
        'Tahun Ajaran', 
        'Bulan', 
        'Tanggal Bayar', 
        'Jumlah Bayar (Rp)'
    ]);
    
    // Get all payment transactions with student and year info
    $stmt = $pdo->prepare("
        SELECT 
            ps.id, 
            s.nis, 
            s.nama as nama_siswa, 
            ta.nama as tahun_ajaran, 
            ps.bulan, 
            ps.tanggal_bayar, 
            ps.jumlah_bayar 
        FROM pembayaran_spp ps 
        JOIN siswa s ON ps.id_siswa = s.id 
        JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id 
        ORDER BY ps.tanggal_bayar DESC, ps.id DESC
    ");
    
    $stmt->execute();
    
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            str_pad($row['id'], 6, '0', STR_PAD_LEFT), // Format ID with leading zeros
            $row['nis'] ?: '-',
            $row['nama_siswa'],
            $row['tahun_ajaran'],
            $row['bulan'],
            date('d/m/Y', strtotime($row['tanggal_bayar'])), // Format date
            number_format($row['jumlah_bayar'], 0, ',', '.') // Format currency
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    // Clear any output and show error
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Error</h1>';
    echo '<p>Terjadi kesalahan saat mengekspor data: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><a href="../spp-reports">Kembali ke Laporan</a></p>';
}

exit;
