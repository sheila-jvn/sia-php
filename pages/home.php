<?php
$pageTitle = "Home Page";

ob_start();
?>

    <body class="bg-secondary-100 min-h-screen flex items-center justify-center">
    <div class="max-w-4xl w-full mx-auto flex flex-col md:flex-row items-center bg-white rounded-lg shadow-lg overflow-hidden mt-12">
        <div class="w-full md:w-1/2 p-8 flex flex-col justify-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-primary-700 mb-4">Selamat Datang di<br>SIA Darussolihin
            </h1>
            <p class="text-lg text-secondary-700 mb-8">Sistem Informasi Akademik untuk SMA IT Darussolihin.</p>
            <?php require_once __DIR__ . '/../lib/config.php'; ?>
            <a href="<?= htmlspecialchars($urlPrefix) ?>/login"
               class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-8 rounded shadow transition text-lg">Login</a>
        </div>
        <div class="w-full md:w-1/2 flex items-center justify-center p-8">
            <img src="https://files.catbox.moe/obe2a7.jpg" alt="Logo SMA IT Darussolihin"
                 class="max-w-xs w-40 h-40 md:w-56 md:h-56 object-cover rounded-full mx-auto shadow">
        </div>
    </div>
    </body>

<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
