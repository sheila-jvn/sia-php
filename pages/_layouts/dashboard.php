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
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="nav-link <?= $currentPage === 'students' ? 'active' : 'link-dark' ?>">Students</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/teachers" class="nav-link <?= $currentPage === 'teachers' ? 'active' : 'link-dark' ?>">Teachers</a>
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