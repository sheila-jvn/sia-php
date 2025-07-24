<?php

if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../lib/config.php';
header('Location: ' . $urlPrefix . '/login');
    exit();
}

$pageTitle = "Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
     <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 2em; }
    </style>
</head>
<body>
    <h1>Welcome to your Dashboard!</h1>
    <p>This is a protected page. You can only see it because you are logged in.</p>
    <p>Your User ID is: <?= htmlspecialchars($_SESSION['user_id']) ?></p>
    <?php require_once __DIR__ . '/../lib/config.php'; ?>
    <a href="<?= htmlspecialchars($urlPrefix) ?>/logout">Logout</a>
</body>
</html>
