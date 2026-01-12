<?php
// public/api/guest-events.php
// API do zarządzania wydarzeniami gościa (zapisywanie, usuwanie, sprawdzanie)

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';

// Start sesji
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];

// POST - zapisz wydarzenie w sesji
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = isset($data['event_id']) ? (int)$data['event_id'] : null;
    
    if (!$eventId || $eventId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowe ID wydarzenia']);
        exit;
    }
    
    // Inicjalizuj tablicę w sesji jeśli nie istnieje
    if (!isset($_SESSION['guest_pending_events'])) {
        $_SESSION['guest_pending_events'] = [];
    }
    
    // Dodaj wydarzenie jeśli jeszcze nie ma
    if (!in_array($eventId, $_SESSION['guest_pending_events'])) {
        $_SESSION['guest_pending_events'][] = $eventId;
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Wydarzenie zapisane',
        'pending_events' => $_SESSION['guest_pending_events']
    ]);
    exit;
}

// DELETE - usuń wydarzenie z sesji
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = isset($data['event_id']) ? (int)$data['event_id'] : null;
    
    if (!$eventId || $eventId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowe ID wydarzenia']);
        exit;
    }
    
    // Usuń wydarzenie z sesji
    if (isset($_SESSION['guest_pending_events'])) {
        $key = array_search($eventId, $_SESSION['guest_pending_events']);
        if ($key !== false) {
            unset($_SESSION['guest_pending_events'][$key]);
            // Reindeksuj tablicę po usunięciu
            $_SESSION['guest_pending_events'] = array_values($_SESSION['guest_pending_events']);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Wydarzenie usunięte',
        'pending_events' => $_SESSION['guest_pending_events'] ?? []
    ]);
    exit;
}

// GET - sprawdź czy wydarzenie jest zapisane lub pobierz wszystkie
if ($method === 'GET') {
    require_once __DIR__ . '/../../src/Helpers/url.php';
    require_once __DIR__ . '/../../src/classes/Database.php';
    
    $pendingEvents = $_SESSION['guest_pending_events'] ?? [];
    
    // Wyczyść zakończone wydarzenia z sesji
    if (!empty($pendingEvents)) {
        $placeholders = implode(',', array_fill(0, count($pendingEvents), '?'));
        if ($placeholders) {
            $activeEvents = Database::query(
                "SELECT event_id FROM Events 
                 WHERE event_id IN ($placeholders) 
                 AND (end_datetime IS NULL OR end_datetime > NOW())",
                $pendingEvents
            );
            $activeEventIds = array_map(function($e) { return (int)$e['event_id']; }, $activeEvents ?: []);
            if (count($activeEventIds) !== count($pendingEvents)) {
                $_SESSION['guest_pending_events'] = $activeEventIds;
                $pendingEvents = $activeEventIds;
            }
        }
    }
    
    // Jeśli podano details=1, zwróć szczegóły zapisanych wydarzeń
    if (isset($_GET['details']) && $_GET['details'] == '1') {
        require_once __DIR__ . '/../../src/Helpers/pending_events.php';
        $details = getPendingEventsDetails();
        
        echo json_encode([
            'success' => true,
            'details' => $details,
            'count' => count($details)
        ]);
        exit;
    }
    
    // Jeśli podano event_id, sprawdź czy jest na liście
    if (isset($_GET['event_id'])) {
        $eventId = (int)$_GET['event_id'];
        $isSaved = in_array($eventId, $pendingEvents);
        
        echo json_encode([
            'success' => true,
            'event_id' => $eventId,
            'is_saved' => $isSaved,
            'pending_events' => $pendingEvents
        ]);
        exit;
    }
    
    // Zwróć wszystkie zapisane wydarzenia
    echo json_encode([
        'success' => true,
        'pending_events' => $pendingEvents,
        'count' => count($pendingEvents)
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Metoda niedozwolona']);
