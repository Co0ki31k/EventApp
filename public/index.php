<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';

// helper for building URLs/assets
require_once SRC_PATH . '/Helpers/url.php';

// start sesji
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once VIEWS_PATH . '/partials/head.php';

require_once VIEWS_PATH . '/guest/landing.php';