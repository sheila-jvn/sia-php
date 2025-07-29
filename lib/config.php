<?php
// lib/config.php
// Shared configuration for the app

// Database configuration
$config = [
    'host' => '127.0.0.1',
    'dbname' => 'sia_php',
    'user' => 'root',
    'password' => '',
];

// Set this to the part of the URL path that comes before your routes.
// For example, if your login URL is:
// http://localhost/sia-project/public/login
// ...then set this variable to '/sia-project/public'
$urlPrefix = '/sia/public';

