<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../lib/database.php';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
if (empty($username) || empty($password)) {
    $error = 'Username and password are required.';
} else {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id, password FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        require_once __DIR__ . '/../lib/config.php';
        header('Location: ' . $urlPrefix . '/dashboard');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}}
$pageTitle = "Login";
ob_start();
?>
<main>
<form method="POST" action="<?= htmlspecialchars($urlPrefix) ?>/login" class="w-100" style="max-width:400px; margin:auto;">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="text-danger"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</main>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
