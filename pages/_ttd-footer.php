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
$lokasi = $lokasi ?? 'Jakarta';
?>

<div class="mt-8">
    <div class="grid grid-cols-2 gap-8 px-8">
        <div></div>
        <div class="text-center w-48 justify-self-center">
            <p class="text-sm text-secondary-700"><?= htmlspecialchars($lokasi) ?>, <?= htmlspecialchars($hariIni) ?>, <?= htmlspecialchars($tanggalIni) ?></p>
        </div>
        <div class="text-center w-48 justify-self-center">
            <p class="text-sm font-medium text-secondary-700">Pimpinan Yayasan</p>
            <div class="mt-12 border-b border-secondary-400 w-full"></div>
            <p class="text-sm font-medium text-secondary-700 mt-2">Hasan Basri S.Pd</p>
        </div>
        <div class="text-center w-48 justify-self-center">
            <p class="text-sm font-medium text-secondary-700">Mengetahui Staff TU</p>
            <div class="mt-12 border-b border-secondary-400 w-full"></div>
            <p class="text-sm font-medium text-secondary-700 mt-2">Sasqi</p>
        </div>
    </div>
</div>
