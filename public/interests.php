<?php
/**
 * Strona wyboru zainteresowań po rejestracji
 */

// Załaduj konfigurację
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';

// Załaduj klasy
load_class('Database');
load_class('Security');
require_once SRC_PATH . '/Models/Category.php';
require_once SRC_PATH . '/Controllers/Auth/InterestsController.php';

// Startuj sesję z odpowiednią nazwą
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

// Sprawdź czy użytkownik przeszedł przez proces rejestracji
if (!isset($_SESSION['temp_user_id'])) {
    header('Location: login.php');
    exit;
}

// Określ krok procesu
$step = $_GET['step'] ?? 'categories';

// Obsługa różnych kroków
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 'categories' || !isset($_GET['step'])) {
        // Krok 1: Przetwarzanie wyboru kategorii
        InterestsController::processCategories();
    } elseif ($step === 'subcategories') {
        // Krok 2: Przetwarzanie wyboru podkategorii
        InterestsController::processSubcategories();
    }
} else {
    // GET - wyświetlanie formularzy
    if ($step === 'subcategories' && isset($_SESSION['selected_categories'])) {
        // Krok 2: Wyświetl formularz wyboru podkategorii
        InterestsController::showSubcategories();
    } else {
        // Krok 1: Wyświetl formularz wyboru kategorii
        InterestsController::showCategories();
    }
}
