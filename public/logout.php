<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

// Wyczyść sesję
session_unset();
session_destroy();

// Przekieruj do strony głównej
header('Location: index.php');
exit;
