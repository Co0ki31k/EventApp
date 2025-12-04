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
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa wydarzeń - EventApp</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
</head>
<body class="map-page">
    <!-- Navbar -->
    <nav class="map-navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <h1>EventApp</h1>
            </div>
            <div class="navbar-user">
                <span class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn-logout">Wyloguj</a>
            </div>
        </div>
    </nav>

    <!-- Mapa -->
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/map/map-init.js') ?>"></script>
</body>
</html>
