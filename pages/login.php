<?php

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    require_once __DIR__ . '/../lib/database.php';
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $pdo = getDbConnection();
        
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            
            require_once __DIR__ . '/../lib/config.php';
header('Location: ' . $urlPrefix . '/dashboard');
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = "Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        body { font-family: sans-serif; display: grid; place-content: center; min-height: 100vh; }
        form { display: flex; flex-direction: column; gap: 0.5em; border: 1px solid #ccc; padding: 2em; border-radius: 8px; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../lib/config.php'; ?>
    <form method="POST" action="<?= htmlspecialchars($urlPrefix) ?>/login">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <div>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</body>
</html>
