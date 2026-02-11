<?php
$schoolProfile = $schoolProfile ?? [
    'name' => 'SMA IT Darussolihin',
    'address' => 'Alamat sekolah',
    'phone' => 'Telp: -',
    'email' => 'Email: -',
    'logo' => 'https://files.catbox.moe/z5o2td.png',
];
?>

<div class="grid grid-cols-[80px_1fr] items-center gap-4 border-b border-secondary-300 pb-4 mb-6">
    <div class="h-20 w-20 rounded-full border border-secondary-200 bg-white overflow-hidden flex items-center justify-center">
        <img src="<?= htmlspecialchars($schoolProfile['logo']) ?>" alt="Logo <?= htmlspecialchars($schoolProfile['name']) ?>" class="h-16 w-16 object-contain">
    </div>
    <div class="text-center">
        <div class="text-lg font-semibold tracking-wide text-secondary-900 uppercase">
            <?= htmlspecialchars($schoolProfile['name']) ?>
        </div>
        <div class="text-sm text-secondary-700">Alamat: <?= htmlspecialchars($schoolProfile['address']) ?></div>
        <div class="text-sm text-secondary-700">
            <?= htmlspecialchars($schoolProfile['phone']) ?> | <?= htmlspecialchars($schoolProfile['email']) ?>
        </div>
    </div>
</div>
