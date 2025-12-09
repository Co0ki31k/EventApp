<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

require_once VIEWS_USER_PATH . '/home.php';

?>
