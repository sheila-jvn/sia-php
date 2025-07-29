<?php
require_once __DIR__ . '/../lib/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $urlPrefix . '/login');
    exit();
}
$pageTitle = "Dashboard";
$currentPage = 'dashboard';
ob_start();
?>
<h1>Welcome to your Dashboard!</h1>
<p>This is a protected page. You can only see it because you are logged in.</p>
<p>Your User ID is: <?= htmlspecialchars($_SESSION['user_id']) ?></p>
<?php
$pageContent = ob_get_clean();
$layout = 'dashboard';
require __DIR__ . '/_layout.php';
