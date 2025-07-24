<?php
if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../lib/config.php';
    header('Location: ' . $urlPrefix . '/login');
    exit();
}
$pageTitle = "Dashboard";
ob_start();
?>
<main>
    <h1>Welcome to your Dashboard!</h1>
    <p>This is a protected page. You can only see it because you are logged in.</p>
    <p>Your User ID is: <?= htmlspecialchars($_SESSION['user_id']) ?></p>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/logout">Logout</a>
</main>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
