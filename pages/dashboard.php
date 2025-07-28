<?php
if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../lib/config.php';
    header('Location: ' . $urlPrefix . '/login');
    exit();
}
$pageTitle = "Dashboard";
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
                <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard" class="nav-link active">Dashboard</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/students" class="nav-link link-dark">Students</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/logout" class="nav-link link-dark">Logout</a>
            </li>
        </ul>
    </div>
    <div class="flex-grow-1 p-4">
        <h1>Welcome to your Dashboard!</h1>
        <p>This is a protected page. You can only see it because you are logged in.</p>
        <p>Your User ID is: <?= htmlspecialchars($_SESSION['user_id']) ?></p>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/_layout.php';
