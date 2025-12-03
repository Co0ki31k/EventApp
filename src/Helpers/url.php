<?php
// Small URL helpers for views
if (!function_exists('asset')) {
    function asset(string $path): string {
        if (defined('ASSETS_URL')) return rtrim(ASSETS_URL, '/') . '/' . ltrim($path, '/');
        if (defined('BASE_URL')) return rtrim(BASE_URL, '/') . '/assets/' . ltrim($path, '/');
        return 'assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        if (defined('BASE_URL')) return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
        $p = ltrim($path, '/');
        return $p === '' ? '/' : $p;
    }
}

if (!function_exists('view')) {
    function view(string $path): string {
        if (defined('VIEWS_PATH')) return rtrim(VIEWS_PATH, '/') . '/' . ltrim($path, '/');
        if (defined('BASE_PATH')) return rtrim(BASE_PATH, '/') . '/src/Views/' . ltrim($path, '/');
        return 'src/Views/' . ltrim($path, '/');
    }
}

if (!function_exists('config')) {
    function config(string $file): string {
        if (defined('CONFIG_PATH')) return rtrim(CONFIG_PATH, '/') . '/' . ltrim($file, '/');
        if (defined('BASE_PATH')) return rtrim(BASE_PATH, '/') . '/config/' . ltrim($file, '/');
        return __DIR__ . '/../../config/' . ltrim($file, '/');
    }
}

if (!function_exists('load_class')) {
    function load_class(string $className): void {
        $path = defined('BASE_PATH') 
            ? rtrim(BASE_PATH, '/') . '/src/classes/' . ltrim($className, '/') . '.php'
            : __DIR__ . '/../src/classes/' . ltrim($className, '/') . '.php';
        
        require_once $path;
    }
}
