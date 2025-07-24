<?php $pageTitle = "Home Page"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 2em; }
        nav a { margin-right: 1em; }
    </style>
</head>
<body>
    <h1>Welcome to the Home Page</h1>
    <p>This is a public page that anyone can see.</p>
    <nav>
        <?php require_once __DIR__ . '/../lib/config.php'; ?>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/login">Login</a>
        <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard">Go to Dashboard (requires login)</a>
    </nav>
</body>
</html>
