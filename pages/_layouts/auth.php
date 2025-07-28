<?php
$authContent = $pageContent;

ob_start();
?>
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <?= $authContent ?>
            </div>
        </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();

extendLayout('base');