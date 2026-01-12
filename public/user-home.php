<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';
load_class('Security');
load_class('Database');

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

$addEventErrors = [];
$addEventsuccessMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    require_once SRC_PATH . '/Controllers/EventsController.php';
    load_class('Security');
    $result = (new EventsController())->create();
    $addEventErrors = $result['errors'] ?? [];
    
    if (!empty($result['success'])) {
        $_SESSION['event_created'] = true;
        $_SESSION['event_message'] = $result['message'] ?? 'Wydarzenie zostało utworzone.';
        
        // Redirect to same page (without POST data)
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

$errors = $addEventErrors ?? [];
$successMessage = null;
if(session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['event_created'])){
    $successMessage = $_SESSION['event_message'] ?? ['Wydarzenie zostalo utworzone'];
    unset($_SESSION['event_created'],$_SESSION['event_message']);
}

$joinedEventsMessage = null;
if(session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['joined_events_message'])){
    $joinedEventsMessage = $_SESSION['joined_events_message'];
    unset($_SESSION['joined_events_message']);
}

require_once VIEWS_USER_PATH . '/home.php';

?>
