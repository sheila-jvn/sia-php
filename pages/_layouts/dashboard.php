<?php
global $urlPrefix, $currentPage;
$dashboardContent = $pageContent;

ob_start();
?>
<div class="d-flex">
    <div class="flex-shrink-0 p-3 bg-light" style="width: 220px; min-height: 100vh;">
        <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <span class="fs-4">SIA</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : 'link-dark' ?>">Dashboard</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="nav-link <?= $currentPage === 'students' ? 'active' : 'link-dark' ?>">Siswa</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="nav-link <?= $currentPage === 'teachers' ? 'active' : 'link-dark' ?>">Guru</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/absensi" class="nav-link <?= $currentPage === 'absensi' ? 'active' : 'link-dark' ?>">Absensi</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/classes" class="nav-link <?= $currentPage === 'classes' ? 'active' : 'link-dark' ?>">Kelas</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/nilai" class="nav-link <?= $currentPage === 'nilai' ? 'active' : 'link-dark' ?>">Nilai</a>
            </li>
            <li class="mb-1">
                <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#spp-collapse" aria-expanded="false" style="background: none; color: inherit; text-decoration: none; padding: 8px 12px; width: 100%; text-align: left;">
                    Pembayaran SPP
                </button>
                <div class="collapse" id="spp-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small" style="margin-left: 16px;">
                        <li><a href="<?= htmlspecialchars($urlPrefix) ?>/spp-students" class="nav-link <?= $currentPage === 'spp-students' ? 'active' : 'link-dark' ?> d-inline-flex text-decoration-none rounded">Daftar Siswa</a></li>
                        <li><a href="<?= htmlspecialchars($urlPrefix) ?>/spp-history" class="nav-link <?= $currentPage === 'spp-history' ? 'active' : 'link-dark' ?> d-inline-flex text-decoration-none rounded">Riwayat Cicilan</a></li>
                        <li><a href="<?= htmlspecialchars($urlPrefix) ?>/spp-reports" class="nav-link <?= $currentPage === 'spp-reports' ? 'active' : 'link-dark' ?> d-inline-flex text-decoration-none rounded">Laporan/Export</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/logout" class="nav-link link-dark">Logout</a>
            </li>
        </ul>
    </div>
    <div class="flex-grow-1 p-4" style="min-width:0; overflow-x:auto;">
        <?= $dashboardContent ?>
    </div>
</div>
<?php
$pageContent = ob_get_clean();

extendLayout('base');