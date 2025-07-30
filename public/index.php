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

    case '/students/create':
        require __DIR__ . '/../pages/students-create.php';
        break;

    case '/students/details':
        require __DIR__ . '/../pages/students-details.php';
        break;

    case '/students/edit':
        require __DIR__ . '/../pages/students-edit.php';
        break;

    case '/teachers':
        require __DIR__ . '/../pages/teachers.php';
        break;

    case '/teachers/create':
        require __DIR__ . '/../pages/teachers-create.php';
        break;

    case '/teachers/details':
        require __DIR__ . '/../pages/teachers-details.php';
        break;

    case '/teachers/edit':
        require __DIR__ . '/../pages/teachers-edit.php';
        break;

    case '/teachers/delete':
        require __DIR__ . '/../pages/teachers-delete.php';
        break;
    
    case '/teachers/export':
        require __DIR__ . '/../pages/teachers-export.php';
        break;

    case '/students/export':
        require __DIR__ . '/../pages/students-export.php';
        break;

    case '/students/delete':
        require __DIR__ . '/../pages/students-delete.php';
        break;

    case '/nilai':
        require __DIR__ . '/../pages/nilai.php';
        break;

    case '/nilai/create':
        require __DIR__ . '/../pages/nilai-create.php';
        break;

    case '/nilai/details':
        require __DIR__ . '/../pages/nilai-details.php';
        break;

    case '/nilai/edit':
        require __DIR__ . '/../pages/nilai-edit.php';
        break;

    case '/nilai/delete':
        require __DIR__ . '/../pages/nilai-delete.php';
        break;
    
    case '/nilai/export':
        require __DIR__ . '/../pages/nilai-export.php';
        break;

    case '/classes':
        require __DIR__ . '/../pages/classes.php';
        break;

    case '/classes/create':
        require __DIR__ . '/../pages/classes-create.php';
        break;

    case '/classes/details':
        require __DIR__ . '/../pages/classes-details.php';
        break;

    case '/classes/edit':
        require __DIR__ . '/../pages/classes-edit.php';
        break;

    case '/classes/delete':
        require __DIR__ . '/../pages/classes-delete.php';
        break;
    
    case '/classes/export':
        require __DIR__ . '/../pages/classes-export.php';
        break;
    
    case '/absensi':
        require __DIR__ . '/../pages/absensi.php';
        break;

    case '/absensi/create':
        require __DIR__ . '/../pages/absensi-create.php';
        break;

    case '/absensi/details':
        require __DIR__ . '/../pages/absensi-details.php';
        break;

    case '/absensi/edit':
        require __DIR__ . '/../pages/absensi-edit.php';
        break;

    case '/absensi/delete':
        require __DIR__ . '/../pages/absensi-delete.php';
        break;
    
    case '/absensi/export':
        require __DIR__ . '/../pages/absensi-export.php';
        break;

    case '/spp-students':
        require __DIR__ . '/../pages/spp-students.php';
        break;

    case '/spp-status':
        require __DIR__ . '/../pages/spp-status.php';
        break;

    case '/spp-pay':
        require __DIR__ . '/../pages/spp-pay.php';
        break;

    case '/spp-history':
        require __DIR__ . '/../pages/spp-history.php';
        break;

    case '/spp-reports':
        require __DIR__ . '/../pages/spp-reports.php';
        break;

    default:
        http_response_code(404);
        echo '<h1>404 Page Not Found</h1>';
        break;
}
