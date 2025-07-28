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
<main class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
  <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
    <div class="card-body p-4">
      <div class="text-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" class="mb-2" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
        <h2 class="h4 mb-0">Login</h2>
      </div>
      <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="<?= htmlspecialchars($urlPrefix) ?>/login" autocomplete="on" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" id="username" name="username" class="form-control" required autofocus autocomplete="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div class="mt-3 text-center">
          <a href="#" class="small text-decoration-none">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
</main>
<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
