<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';
load_class('Security');
load_class('Database');

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Update last_login timestamp
try {
    Database::query(
        "UPDATE Users SET last_login = NOW() WHERE user_id = ?",
        [(int)$_SESSION['user_id']]
    );
} catch (Exception $e) {
    // Log error but don't block page load
    error_log('Failed to update last_login: ' . $e->getMessage());
}

require_once VIEWS_USER_PATH . '/home.php';

?>
