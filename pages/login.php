<?php
require_once __DIR__ . '/../lib/config.php';
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

        // Warning: This is a direct comparison for demonstration purposes ONLY.
        // In a real application, you must use password_verify() with hashed passwords.
        if ($user && $password === $user['password']) {
            session_start(); // Make sure session is started before setting session variables
            $_SESSION['user_id'] = $user['id'];
            header('Location: ' . $urlPrefix . '/dashboard');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
$pageTitle = "Login";
ob_start();
?>

<main class="flex items-center justify-center min-h-screen bg-secondary-100">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-6">
            <iconify-icon icon="mdi:account-circle" width="48" height="48" class="text-primary-600 mb-2"></iconify-icon>
            <h2 class="text-2xl font-bold text-primary-700 mb-0">Login</h2>
        </div>
        <?php if ($error): ?>
            <div class="mb-4 rounded-lg bg-error-100 text-error-700 px-4 py-3 text-sm text-center border border-error-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="<?= htmlspecialchars($urlPrefix) ?>/login" autocomplete="on" novalidate>
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-primary-700 mb-1">Username</label>
                <input type="text" id="username" name="username" class="block w-full rounded-md border border-secondary-300 focus:border-primary-600 focus:ring focus:ring-primary-100 px-3 py-2 text-primary-900 bg-secondary-50" required autofocus autocomplete="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-primary-700 mb-1">Password</label>
                <input type="password" id="password" name="password" class="block w-full rounded-md border border-secondary-300 focus:border-primary-600 focus:ring focus:ring-primary-100 px-3 py-2 text-primary-900 bg-secondary-50" required autocomplete="current-password">
            </div>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 rounded-md transition-colors">Login</button>
            <div class="mt-4 text-center">
                <a href="#" class="text-sm text-primary-600 hover:underline">Forgot password?</a>
            </div>
        </form>
    </div>
</main>
<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';