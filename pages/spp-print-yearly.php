<?php
require_once __DIR__ . '/../lib/database.php';

$yearNum = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

$pdo = getDbConnection();

// Get all payments for this year
$stmt = $pdo->prepare('SELECT ps.*, s.nis, s.nama as nama_siswa, ta.nama as tahun_ajaran FROM pembayaran_spp ps JOIN siswa s ON ps.id_siswa = s.id JOIN tahun_ajaran ta ON ps.id_tahun_ajaran = ta.id WHERE YEAR(ps.tanggal_bayar) = ? ORDER BY s.nis ASC, ps.bulan ASC, ps.tanggal_bayar ASC');
$stmt->execute([$yearNum]);
$payments = $stmt->fetchAll();

?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tahunan SPP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4">
    <style>
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="bg-white text-secondary-900">
    <div class="max-w-5xl mx-auto my-8 p-8 border border-secondary-300 rounded-lg shadow-lg bg-white">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary-800">Laporan Pembayaran SPP Tahun <?= $yearNum ?></h1>
            <button onclick="window.print()" class="no-print px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Print</button>
        </div>
        <div class="mb-4">
            <table class="min-w-full text-sm border border-secondary-200 rounded">
                <thead class="bg-secondary-100">
                    <tr>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">NIS</th>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Nama Siswa</th>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Tahun Ajaran</th>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Bulan</th>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Tanggal Bayar</th>
                        <th class="px-3 py-2 border-b border-secondary-200 text-left">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['nis']) ?></td>
                            <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['nama_siswa']) ?></td>
                            <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['tahun_ajaran']) ?></td>
                            <td class="px-3 py-2 border-b border-secondary-100"><?= htmlspecialchars($p['bulan']) ?></td>
                            <td class="px-3 py-2 border-b border-secondary-100"><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></td>
                            <td class="px-3 py-2 border-b border-secondary-100">Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-secondary-500">Tidak ada pembayaran tahun ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-8 text-right text-xs text-secondary-500">
            Dicetak pada <?= date('d/m/Y H:i') ?>
        </div>
    </div>
</body>
</html>
