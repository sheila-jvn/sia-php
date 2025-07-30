<?php
$authContent = $pageContent;

ob_start();
?>
<div class="min-h-screen flex items-center justify-center bg-secondary-100">
    <div class="w-full px-4">
        <div class="mx-auto max-w-md w-full">
            <?= $authContent ?>
        </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();

extendLayout('base');