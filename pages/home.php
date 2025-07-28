<?php
$pageTitle = "Home Page";

ob_start();
?>
<main>
    <h1 class="text-primary">Welcome to the Home Page</h1>
    <p>This is a public page that anyone can see.</p>
    <?php require_once __DIR__ . '/../lib/config.php'; ?>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/login">Login</a>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard">Go to Dashboard (requires login)</a>
</main>
<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
