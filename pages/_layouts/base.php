<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Untitled' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Teacher/Global Style Override -->
    <style>
        :root {
            --primary-blue: #2c5282;
            --light-blue: #ebf8ff;
            --text-color: #4a5568;
            --border-color: #cbd5e0;
            --bg-color: #f7fafc;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        .card {
            border-color: var(--border-color);
        }
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: #fff;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #2a4365;
            border-color: #2a4365;
        }
        .btn-info {
            background-color: #3182ce;
            border-color: #3182ce;
            color: #fff;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }
        .btn-info:hover {
            background-color: #2c5282;
            border-color: #2c5282;
        }
        .btn-warning {
            background-color: #f6ad55;
            border-color: #f6ad55;
            color: #fff;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }
        .btn-warning:hover {
            background-color: #ed8936;
            border-color: #ed8936;
        }
        .btn-danger {
            background-color: #e53e3e;
            border-color: #e53e3e;
            color: #fff;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }
        .btn-danger:hover {
            background-color: #c53030;
            border-color: #c53030;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.25);
        }
        .text-decoration-none {
            color: var(--primary-blue) !important;
        }
        .text-decoration-none:hover {
            text-decoration: underline !important;
        }
        /* Sidebar active link override to match primary button color */
        .nav-pills .nav-link.active,
        .nav-pills .show > .nav-link {
            background-color: var(--primary-blue) !important;
            color: #fff !important;
            border-color: var(--primary-blue) !important;
        }
    </style>
</head>

<body>
    <?= $pageContent ?? '' ?>
</body>

</html>