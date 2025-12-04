<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';

// Załaduj klasy
load_class('Database');
load_class('Security');
require_once SRC_PATH . '/Models/User.php';
require_once SRC_PATH . '/Controllers/Auth/RegisterController.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

// Inicjalizacja zmiennych
$errors = [];
$success = false;
$message = '';
$old = [];

// Obsługa POST - rejestracja
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new RegisterController();
    $result = $controller->register();
    
    if ($result['success']) {
        $success = true;
        $message = $result['message'];
        
        // Zapisz ID użytkownika w sesji tymczasowej
        $_SESSION['temp_user_id'] = $result['user_id'];
        
        // Przekieruj do wyboru zainteresowań
        header('Location: interests.php');
        exit;
    } else {
        $errors = $result['errors'];
        $message = $result['message'];
        // Zachowaj wprowadzone dane (bez hasła)
        $old = $controller->getData();
        unset($old['password']);
    }
}

// Render registration view
require_once VIEWS_PATH . '/auth/register.php';
