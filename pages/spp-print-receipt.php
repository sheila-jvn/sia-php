<?php
require_once __DIR__ . '/../lib/database.php';

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$yearId = isset($_GET['year_id']) ? (int)$_GET['year_id'] : 0;
$month = isset($_GET['month']) ? $_GET['month'] : '';

if (!$studentId || !$yearId || !$month) {
    die('Parameter tidak lengkap.');
}

$pdo = getDbConnection();

// Get student info
$stmt = $pdo->prepare('SELECT * FROM siswa WHERE id = ?');
$stmt->execute([$studentId]);
$student = $stmt->fetch();
if (!$student) die('Siswa tidak ditemukan.');

// Get year info
$stmt = $pdo->prepare('SELECT * FROM tahun_ajaran WHERE id = ?');
$stmt->execute([$yearId]);
$year = $stmt->fetch();
if (!$year) die('Tahun ajaran tidak ditemukan.');

// Get payments for this month
$stmt = $pdo->prepare('SELECT * FROM pembayaran_spp WHERE id_siswa = ? AND id_tahun_ajaran = ? AND bulan = ? ORDER BY tanggal_bayar');
$stmt->execute([$studentId, $yearId, $month]);
$payments = $stmt->fetchAll();
if (!$payments) die('Tidak ada pembayaran untuk bulan ini.');

$sppAmount = 650000;
$totalPaid = array_sum(array_column($payments, 'jumlah_bayar'));
$outstanding = max(0, $sppAmount - $totalPaid);

?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kwitansi SPP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="bg-white text-secondary-900">
    <div class="max-w-lg mx-auto my-8 p-8 border border-secondary-300 rounded-lg shadow-lg bg-white">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary-800">Kwitansi Pembayaran SPP</h1>
            <button onclick="window.print()" class="no-print px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Print</button>
        </div>
        <div class="mb-4">
            <div class="mb-1"><span class="font-semibold">Nama Siswa:</span> <?= htmlspecialchars($student['nama']) ?></div>
            <div class="mb-1"><span class="font-semibold">NIS:</span> <?= htmlspecialchars($student['nis'] ?? '-') ?></div>
            <div class="mb-1"><span class="font-semibold">Tahun Ajaran:</span> <?= htmlspecialchars($year['nama']) ?></div>
            <div class="mb-1"><span class="font-semibold">Bulan:</span> <?= htmlspecialchars($month) ?></div>
        </div>
        <div class="mb-4">
            <table class="min-w-full text-sm border border-secondary-200 rounded">
                <thead class="bg-secondary-100">
                    <tr>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Tanggal Bayar</th>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td class="px-3 py-2 border-b border-secondary-100"><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></td>
                            <td class="px-3 py-2 border-b border-secondary-100">Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mb-4">
            <div><span class="font-semibold">Total Dibayar:</span> Rp <?= number_format($totalPaid, 0, ',', '.') ?></div>
            <div><span class="font-semibold">Sisa:</span> <?= $outstanding > 0 ? 'Rp ' . number_format($outstanding, 0, ',', '.') : '<span class=\'text-status-success-600 font-semibold\'>Lunas</span>' ?></div>
        </div>
        <div class="mt-8 text-right text-xs text-secondary-500">
            Dicetak pada <?= date('d/m/Y H:i') ?>
        </div>
    </div>
</body>
</html>
