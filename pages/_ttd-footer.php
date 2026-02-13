<?php
$hariIndonesia = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];

$bulanIndonesia = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$hariIni = $hariIndonesia[date('l')] ?? date('l');
$tanggalIni = date('d') . ' ' . $bulanIndonesia[(int)date('n')] . ' ' . date('Y');
$lokasi = $lokasi ?? 'Bogor';
?>

<div class="mt-8">
    <div class="flex justify-between px-8">
        <div class="text-center w-48">
            <p class="text-sm font-medium text-secondary-700">Ketua Yayasan</p>
            <div class="mt-12 border-b border-secondary-400 w-full"></div>
        </div>
        <div class="text-center w-48">
            <p class="text-sm text-secondary-700"><?= htmlspecialchars($lokasi) ?>, <?= htmlspecialchars($hariIni) ?>, <?= htmlspecialchars($tanggalIni) ?></p>
            <p class="text-sm font-medium text-secondary-700 mt-1">Mengetahui</p>
            <div class="mt-12 border-b border-secondary-400 w-full"></div>
        </div>
    </div>
</div>
