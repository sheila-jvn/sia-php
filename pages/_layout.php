<?php
function renderLayout($layoutName = null) {
    global $pageTitle, $pageContent;
    
    if ($layoutName === null) {
        $layoutName = 'base';
    }
    
    $layoutFile = __DIR__ . "/_layouts/{$layoutName}.php";
    
    if (!file_exists($layoutFile)) {
        throw new Exception("Layout '{$layoutName}' not found at {$layoutFile}");
    }
    
    require $layoutFile;
}

if (!function_exists('extendLayout')) {
    function extendLayout($parentLayout) {
        global $pageTitle, $pageContent;
        
        $childContent = $pageContent;
        $pageContent = $childContent;
        
        renderLayout($parentLayout);
    }
}

renderLayout($layout ?? 'base');