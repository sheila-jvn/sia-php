<?php
$pageTitle = "Home Page";

ob_start();
?>
<style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e3f2fd;
            display: flex;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
        }

        .left-content {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }

        .right-image {
            background-color: #bbdefb;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .right-image img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        h1 {
            color: #2c5282;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        p {
            color: #4a5568;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: #2c5282;
            color: #fff;
            font-weight: 400;
            border-radius: 0.5rem;
        }

        .btn-primary:hover {
            background-color: #2a4365;
            color: #fff;
        }
    </style>
<body>
    <div class="container">
        <div class="row g-0">
            <div class="col-md-6 left-content">
                <h1>Selamat Datang di SIA</h1>
                <p class="lead">Sistem Informasi Akademik untuk SMA IT Darussolihin.</p>
                <?php require_once __DIR__ . '/../lib/config.php'; ?>
                <a href="<?= htmlspecialchars($urlPrefix) ?>/login" class="btn btn-primary btn-md">Login</a>
                <!-- <a href="<?= htmlspecialchars($urlPrefix) ?>/dashboard" class="btn btn-primary btn-md">Go to Dashboard (requires login)</a> -->
            </div>
            <div class="col-md-6 right-image">
                <img src="{{ asset('images/logo_darussolihin.png') }}" alt="Logo SMA IT Darussolihin" class="img-fluid">
            </div>
        </div>
    </div>
</body>
<?php
$pageContent = ob_get_clean();
$layout = 'base';
require __DIR__ . '/_layout.php';
