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
