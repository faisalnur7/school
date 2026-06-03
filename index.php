<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve static files from public/ when using PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $file = __DIR__ . '/public' . $uri;
    if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
        $mimes = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'json'  => 'application/json',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
            'map'   => 'application/json',
        ];
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (isset($mimes[$ext])) {
            header('Content-Type: ' . $mimes[$ext]);
        }
        readfile($file);
        exit;
    }
}

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
