<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Untitled' ?></title>

    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <!-- Tailwind -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-primary-50: #e4ebfa;
            --color-primary-100: #cddaf6;
            --color-primary-200: #98b7ee;
            --color-primary-300: #5795e5;
            --color-primary-400: #3f71b1;
            --color-primary-500: #2c5282;
            --color-primary-600: #224168;
            --color-primary-700: #193354;
            --color-primary-800: #10243d;
            --color-primary-900: #081628;
            --color-primary-950: #030b18;

            --color-secondary-50: #f5f6f8;
            --color-secondary-100: #ebeef2;
            --color-secondary-200: #d8dde5;
            --color-secondary-300: #c5cdd8;
            --color-secondary-400: #b1bccb;
            --color-secondary-500: #a0aec0;
            --color-secondary-600: #7a8898;
            --color-secondary-700: #5a6471;
            --color-secondary-800: #3c434c;
            --color-secondary-900: #20242a;
            --color-secondary-950: #13161a;

            --color-accent-50: #fff1eb;
            --color-accent-100: #ffe2d7;
            --color-accent-200: #ffc5aa;
            --color-accent-300: #ffa771;
            --color-accent-400: #f88908;
            --color-accent-500: #d97706;
            --color-accent-600: #ad5e04;
            --color-accent-700: #824502;
            --color-accent-800: #5a2e01;
            --color-accent-900: #341800;
            --color-accent-950: #230e00;

            /* Status colors */
            --color-status-success-50: #d4fde3;
            --color-status-success-100: #9efcc3;
            --color-status-success-200: #56ec9c;
            --color-status-success-300: #4cd38b;
            --color-status-success-400: #41b878;
            --color-status-success-500: #38a169;
            --color-status-success-600: #2a7e51;
            --color-status-success-700: #1e603d;
            --color-status-success-800: #124128;
            --color-status-success-900: #072515;
            --color-status-success-950: #03170b;

            --color-status-warning-50: #fef5ec;
            --color-status-warning-100: #feebd7;
            --color-status-warning-200: #fcd39f;
            --color-status-warning-300: #fcbf55;
            --color-status-warning-400: #ebae34;
            --color-status-warning-500: #d69e2e;
            --color-status-warning-600: #a97c22;
            --color-status-warning-700: #7d5b17;
            --color-status-warning-800: #543c0c;
            --color-status-warning-900: #302104;
            --color-status-warning-950: #1f1402;

            --color-status-error-50: #fbeeee;
            --color-status-error-100: #f8dcdc;
            --color-status-error-200: #f3bcbc;
            --color-status-error-300: #ee9696;
            --color-status-error-400: #eb7070;
            --color-status-error-500: #e53e3e;
            --color-status-error-600: #b83030;
            --color-status-error-700: #8a2222;
            --color-status-error-800: #631515;
            --color-status-error-900: #3b0909;
            --color-status-error-950: #280404;

            --color-status-info-50: #eef4fd;
            --color-status-info-100: #dde9fc;
            --color-status-info-200: #b9d4f8;
            --color-status-info-300: #96c2f6;
            --color-status-info-400: #66adf2;
            --color-status-info-500: #4299e1;
            --color-status-info-600: #3379b3;
            --color-status-info-700: #245b88;
            --color-status-info-800: #153c5b;
            --color-status-info-900: #092135;
            --color-status-info-950: #041423;
        }
    </style>
</head>

<body>
<?= $pageContent ?? '' ?>
</body>

</html>