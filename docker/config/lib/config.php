<?php
// Docker-specific configuration
// This file shadows lib/config.php only inside Docker containers

$config = [
    'host' => 'mariadb',  // Service name in Docker network
    'dbname' => 'sia_php',
    'user' => 'root',
    'password' => 'root',
];

$urlPrefix = '/sia/public';
