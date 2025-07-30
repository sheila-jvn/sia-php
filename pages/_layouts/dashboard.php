<?php
global $urlPrefix, $currentPage;
$dashboardContent = $pageContent;

ob_start();
?>
    <div class="flex">
        <aside class="flex-shrink-0 p-4 bg-secondary-100 border-r border-secondary-300 min-h-screen w-[220px]">
            <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard"
               class="flex items-center mb-6 text-primary-700 hover:text-primary-900">
                <iconify-icon icon="mdi:school" width="28" height="28" class="mr-2 text-primary-600"></iconify-icon>
                <span class="text-2xl font-bold tracking-tight text-primary-700">SIA</span>
            </a>
            <div class="border-t border-secondary-200 my-4"></div>
            <ul class="flex flex-col gap-1 mb-6">
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'dashboard' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Dashboard</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/students"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'students' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Siswa</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'teachers' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Guru</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'absensi' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Absensi</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/classes"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'classes' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Kelas</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'nilai' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Nilai</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-students"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'spp-students' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Daftar
                        Siswa SPP</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-history"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'spp-history' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Riwayat
                        Cicilan SPP</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/spp-reports"
                       class="block px-4 py-2 rounded-md transition font-medium <?= $currentPage === 'spp-reports' ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-primary-50' ?>">Laporan/Export
                        SPP</a>
                </li>
                <li>
                    <a href="<?= htmlspecialchars($urlPrefix) ?>/logout"
                       class="block px-4 py-2 rounded-md transition font-medium text-error-600 hover:bg-error-50">Logout</a>
                </li>
            </ul>
        </aside>
        <main class="flex-1 p-6 min-w-0 overflow-x-auto">
            <?= $dashboardContent ?>
        </main>
    </div>
<?php
$pageContent = ob_get_clean();

extendLayout('base');