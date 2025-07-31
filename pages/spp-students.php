<?php
$pageTitle = "Pembayaran SPP - Daftar Siswa";
$currentPage = 'spp-students';

require_once __DIR__ . '/../lib/database.php';

$pdo = getDbConnection();

// Get all students with their class information
$sql = "
    SELECT s.id, s.nis, s.nama
    FROM siswa s
    ORDER BY s.nis ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();

// Get current academic year for default selection
$stmt = $pdo->prepare("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC LIMIT 1");
$stmt->execute();
$currentYear = $stmt->fetch();

ob_start();
?>

<div class="max-w-7xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-primary-800">Pembayaran SPP - Daftar Siswa</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-secondary-200">
        <div class="px-6 py-4 border-b border-secondary-200">
            <h2 class="text-xl font-semibold text-secondary-800">Daftar Siswa</h2>
            <?php if ($currentYear): ?>
                <p class="text-sm text-secondary-600 mt-1">Tahun Ajaran: <?= htmlspecialchars($currentYear['nama']) ?></p>
            <?php endif; ?>
        </div>
        <div class="p-6">
            <?php if (empty($students)): ?>
                <div class="bg-accent-100 border border-accent-200 text-accent-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <iconify-icon icon="solar:info-circle-bold" class="mr-2 text-lg"></iconify-icon>
                        Belum ada data siswa.
                    </div>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-primary-100 text-primary-700">
                            <tr>
                                <th class="px-4 py-2 font-semibold">NIS</th>
                                <th class="px-4 py-2 font-semibold">Nama Siswa</th>
                                <th class="px-4 py-2 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                         <tbody class="bg-white divide-y divide-secondary-200">
                            <?php foreach ($students as $student): ?>
                                <tr class="even:bg-secondary-50 hover:bg-secondary-100">
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($student['nis'] ?? '-') ?></td>
                                    <td class="px-4 py-3 text-secondary-900 align-middle"><?= htmlspecialchars($student['nama']) ?></td>
                                    <td class="px-4 py-3 align-middle">
                                        <a href="spp-status?id=<?= $student['id'] ?><?= $currentYear ? '&year=' . $currentYear['id'] : '' ?>" 
                                           class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                                            <iconify-icon icon="solar:cash-out-linear" width="20" height="20"></iconify-icon>
                                            Lihat Pembayaran
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
?>