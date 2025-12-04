<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';

// Załaduj klasy
load_class('Database');
load_class('Security');
require_once SRC_PATH . '/Models/User.php';
require_once SRC_PATH . '/Controllers/Auth/LoginController.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

// Inicjalizacja zmiennych
$errors = [];
$success = false;
$message = '';
$old = [];

// Obsługa POST - logowanie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new LoginController();
    $result = $controller->login();
    
    if ($result['success']) {
        // Zalogowano pomyślnie
        $user = $result['user'];
        
        // Zapisz dane użytkownika w sesji
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Zaktualizuj last_login w bazie
        Database::execute(
            "UPDATE Users SET last_login = NOW() WHERE user_id = ?",
            [$user['user_id']]
        );
        
        // Przekieruj do odpowiedniej strony w zależności od roli
        if ($user['role'] === 'company') {
            header('Location: company-home.php');
        } else {
            header('Location: user-home.php');
        }
        exit;
    } else {
        $errors = $result['errors'];
        $message = $result['message'];
        // Zachowaj wprowadzone dane (bez hasła)
        $old = $controller->getData();
        unset($old['password']);
    }
}

// Render login view
require_once VIEWS_PATH . '/auth/login.php';
