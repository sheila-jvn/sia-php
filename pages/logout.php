<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

session_destroy();

require_once __DIR__ . '/../lib/config.php';
header('Location: ' . $urlPrefix . '/login');
exit();
