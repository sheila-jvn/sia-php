<?php
/**
 * One-off script to populate nilai and pembayaran_spp tables
 * Run with: php scripts/populate_data.php
 */

require_once __DIR__ . '/../lib/config.php';

// Create database connection
try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
        $config['user'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "Connected to database: {$config['dbname']}\n";
    
    // Insert Nilai (Grades) data
    echo "\n--- Inserting Nilai Data ---\n";
    
    $nilaiData = [
        // Siswa 1 (Joko Pintar) - Kelas 10-A (id_kelas=1, id_tahun_ajaran=1)
        [1, 1, 1, 1, 1, 85.5, '2023-09-15', 'Tugas pertama semester'],
        [1, 1, 1, 1, 2, 78.0, '2023-10-05', 'Ulangan bab 1'],
        [1, 1, 1, 1, 3, 88.5, '2023-11-20', ''],
        [1, 2, 1, 1, 1, 92.0, '2023-09-20', ''],
        [1, 2, 1, 1, 2, 89.5, '2023-10-10', ''],
        [1, 2, 1, 1, 4, 85.0, '2023-12-15', 'UAS Semester 1'],
        [1, 3, 1, 1, 1, 80.0, '2023-09-18', ''],
        [1, 3, 1, 1, 2, 76.5, '2023-10-08', ''],
        
        // Siswa 2 (Bambang Ceria) - Kelas 10-B (id_kelas=2, id_tahun_ajaran=1)
        [2, 1, 2, 1, 1, 70.0, '2023-09-15', 'Perlu latihan lebih'],
        [2, 1, 2, 1, 2, 75.5, '2023-10-05', ''],
        [2, 1, 2, 1, 3, 72.0, '2023-11-20', ''],
        [2, 2, 2, 1, 1, 85.0, '2023-09-20', ''],
        [2, 2, 2, 1, 2, 88.0, '2023-10-10', ''],
        [2, 2, 2, 1, 4, 90.5, '2023-12-15', 'UAS Semester 1'],
        [2, 3, 2, 1, 1, 92.0, '2023-09-18', 'Sangat baik'],
        [2, 3, 2, 1, 2, 91.0, '2023-10-08', ''],
        
        // Siswa 3 (Udin Bahagia) - Kelas 11-IPA-1 (id_kelas=3, id_tahun_ajaran=3)
        [3, 4, 3, 3, 1, 82.5, '2024-08-10', ''],
        [3, 4, 3, 3, 2, 79.0, '2024-09-05', ''],
        [3, 4, 3, 3, 3, 86.0, '2024-10-20', ''],
        [3, 4, 3, 3, 5, 90.0, '2024-11-10', 'Praktikum Fisika'],
        [3, 5, 3, 3, 1, 75.0, '2024-08-12', ''],
        [3, 5, 3, 3, 2, 78.5, '2024-09-08', ''],
        [3, 6, 3, 3, 1, 88.0, '2024-08-15', ''],
        [3, 6, 3, 3, 2, 92.5, '2024-09-12', ''],
    ];
    
    $nilaiStmt = $pdo->prepare("
        INSERT INTO nilai 
        (id_siswa, id_mata_pelajaran, id_kelas, id_tahun_ajaran, id_jenis_nilai, nilai, tanggal_penilaian, keterangan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $nilaiCount = 0;
    foreach ($nilaiData as $data) {
        try {
            $nilaiStmt->execute($data);
            $nilaiCount++;
        } catch (PDOException $e) {
            echo "Error inserting nilai: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Inserted $nilaiCount nilai records\n";
    
    // Insert Pembayaran SPP data
    echo "\n--- Inserting Pembayaran SPP Data ---\n";
    
    $sppData = [
        // Siswa 1 - Tahun Ajaran 2023/2024 Ganjil
        [1, 1, 'Juli', '2023-07-05', 150000.00],
        [1, 1, 'Agustus', '2023-08-03', 150000.00],
        [1, 1, 'September', '2023-09-04', 150000.00],
        [1, 1, 'Oktober', '2023-10-02', 150000.00],
        [1, 1, 'November', '2023-11-06', 150000.00],
        [1, 1, 'Desember', '2023-12-04', 150000.00],
        
        // Siswa 2 - Tahun Ajaran 2023/2024 Ganjil
        [2, 1, 'Juli', '2023-07-10', 150000.00],
        [2, 1, 'Agustus', '2023-08-07', 150000.00],
        [2, 1, 'September', '2023-09-05', 150000.00],
        [2, 1, 'Oktober', '2023-10-10', 150000.00],
        [2, 1, 'November', '2023-11-08', 150000.00],
        
        // Siswa 3 - Tahun Ajaran 2024/2025 Ganjil
        [3, 3, 'Juli', '2024-07-08', 175000.00],
        [3, 3, 'Agustus', '2024-08-05', 175000.00],
        [3, 3, 'September', '2024-09-06', 175000.00],
        [3, 3, 'Oktober', '2024-10-04', 175000.00],
    ];
    
    $sppStmt = $pdo->prepare("
        INSERT INTO pembayaran_spp 
        (id_siswa, id_tahun_ajaran, bulan, tanggal_bayar, jumlah_bayar)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $sppCount = 0;
    foreach ($sppData as $data) {
        try {
            $sppStmt->execute($data);
            $sppCount++;
        } catch (PDOException $e) {
            echo "Error inserting SPP: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Inserted $sppCount SPP records\n";
    
    echo "\n--- Summary ---\n";
    echo "Total nilai records: $nilaiCount\n";
    echo "Total SPP records: $sppCount\n";
    echo "\nData population completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
