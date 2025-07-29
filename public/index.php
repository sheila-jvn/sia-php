<?php

session_start();

// ===================================================================
// CONFIGURATION
// ===================================================================
// Set this to the part of the URL path that comes before your routes.
// For example, if your login URL is:
// http://localhost/sia-project/public/login
// ...then set this variable to '/sia-project/public'
require_once __DIR__ . '/../lib/config.php';
// ===================================================================

// --- No need to edit below this line ---

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Determine the route by removing the URL prefix
$route = '/';
if (!empty($urlPrefix) && strpos($requestUri, $urlPrefix) === 0) {
    $route = substr($requestUri, strlen($urlPrefix));
}

// If the route is empty after stripping the prefix, it's the home page
if (empty($route)) {
    $route = '/';
}


// Route the request
switch ($route) {
    case '/':
        require __DIR__ . '/../pages/home.php';
        break;

    case '/login':
        require __DIR__ . '/../pages/login.php';
        break;

    case '/dashboard':
        require __DIR__ . '/../pages/dashboard.php';
        break;

    case '/logout':
        require __DIR__ . '/../pages/logout.php';
        break;

    case '/students':
        require __DIR__ . '/../pages/students.php';
        break;

    case '/students-create':
        require __DIR__ . '/../pages/students-create.php';
        break;

    case '/students-detail':
        require __DIR__ . '/../pages/students-detail.php';
        break;

    case '/teachers':
        require __DIR__ . '/../pages/teachers.php';
        break;

    default:
        http_response_code(404);
        echo '<h1>404 Page Not Found</h1>';
        break;
}
