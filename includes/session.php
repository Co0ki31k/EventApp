<?php
require_once __DIR__ . "/../config/paths.php";
require_once CONFIG_PATH . "/app_config.php";

// Funkcja do przedłużania sesji przy aktywności
function refreshSession() {
    if(session_status() === PHP_SESSION_ACTIVE) {
        setcookie(
            session_name(),       // nazwa sesji
            session_id(),         // aktualny ID sesji
            time() + 60*10,       // nowy czas wygaśnięcia (10 minut)
            '/',
            '',
            false,                // true jeśli HTTPS
            true
        );
    }
}

session_name(SESSION_NAME);

session_set_cookie_params([
    'lifetime' => 60*10,      // 10 minut
    'path' => '/',
    'secure' => false,         // true jeśli HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// przedłużamy sesję przy każdej aktywności
refreshSession();

// prosta funkcja login-check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
