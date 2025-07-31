<?php
require_once __DIR__ . '/../lib/database.php';

try {
    $pdo = getDbConnection();
    
    // Set SPP amount (should match spp-pay.php)
    $sppAmount = 650000;
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="spp_detail_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // Write BOM for UTF-8 to ensure proper encoding in Excel
    fwrite($output, "\xEF\xBB\xBF");
    
    // Header row
    fputcsv($output, [
        'NIS',
        'Nama Siswa', 
        'Tahun Ajaran',
        'Bulan',
        'Total Dibayar (Rp)',
        'Status',
        'Persentase Terbayar (%)'
    ]);
    
    // Get payment summary data with a single optimized query
    $stmt = $pdo->prepare("
        SELECT 
            s.nis,
            s.nama as nama_siswa,
            ta.nama as tahun_ajaran,
            ps.bulan,
            COALESCE(SUM(ps.jumlah_bayar), 0) as total_dibayar
        FROM siswa s
        CROSS JOIN tahun_ajaran ta
        CROSS JOIN (
            SELECT DISTINCT bulan FROM (
                SELECT 'Januari' as bulan UNION ALL
                SELECT 'Februari' UNION ALL
                SELECT 'Maret' UNION ALL
                SELECT 'April' UNION ALL
                SELECT 'Mei' UNION ALL
                SELECT 'Juni' UNION ALL
                SELECT 'Juli' UNION ALL
                SELECT 'Agustus' UNION ALL
                SELECT 'September' UNION ALL
                SELECT 'Oktober' UNION ALL
                SELECT 'November' UNION ALL
                SELECT 'Desember'
            ) months
        ) months
        LEFT JOIN pembayaran_spp ps ON s.id = ps.id_siswa 
            AND ta.id = ps.id_tahun_ajaran 
            AND months.bulan = ps.bulan
        GROUP BY s.id, ta.id, months.bulan
        ORDER BY s.nis ASC, ta.tahun_mulai DESC, 
            FIELD(months.bulan, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')
    ");
    
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    foreach ($results as $row) {
        $totalPaid = (float)$row['total_dibayar'];
        $status = ($totalPaid >= $sppAmount) ? 'Lunas' : 'Belum Lunas';
        $percentage = ($sppAmount > 0) ? round(($totalPaid / $sppAmount) * 100, 2) : 0;
        
        fputcsv($output, [
            $row['nis'] ?: '-',
            $row['nama_siswa'],
            $row['tahun_ajaran'],
            $row['bulan'],
            number_format($totalPaid, 0, ',', '.'),
            $status,
            $percentage
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
