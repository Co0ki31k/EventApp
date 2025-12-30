<?php
// public/api/participants.php
// API do zarządzania uczestnikami wydarzeń

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';

// Start session with same name as rest of app
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];

// GET - pobierz uczestników lub sprawdź status
if ($method === 'GET') {
    $eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
    
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Brak event_id']);
        exit;
    }
    
    try {
        // Jeśli podano user_id - sprawdź czy user jest uczestnikiem
        if ($userId) {
            $result = Database::queryOne(
                "SELECT ep.user_id, ep.joined_at, u.username 
                 FROM EventParticipants ep
                 JOIN Users u ON ep.user_id = u.user_id
                 WHERE ep.event_id = ? AND ep.user_id = ?",
                [$eventId, $userId]
            );
            
            echo json_encode([
                'success' => true,
                'is_joined' => $result !== null,
                'participant' => $result
            ]);
        } else {
            // Pobierz wszystkich uczestników wydarzenia
            $participants = Database::query(
                "SELECT ep.user_id, ep.joined_at, u.username 
                 FROM EventParticipants ep
                 JOIN Users u ON ep.user_id = u.user_id
                 WHERE ep.event_id = ?
                 ORDER BY ep.joined_at ASC",
                [$eventId]
            );
            
            $count = count($participants ?: []);
            
            echo json_encode([
                'success' => true,
                'event_id' => $eventId,
                'count' => $count,
                'participants' => $participants ?: []
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Błąd serwera']);
    }
    exit;
}

// POST - dołącz do wydarzenia
if ($method === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Musisz być zalogowany']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = isset($data['event_id']) ? (int)$data['event_id'] : null;
    $userId = (int)$_SESSION['user_id'];
    
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Brak event_id']);
        exit;
    }
    
    try {
        // Sprawdź czy już jest uczestnikiem
        $exists = Database::queryOne(
            "SELECT 1 FROM EventParticipants WHERE event_id = ? AND user_id = ?",
            [$eventId, $userId]
        );
        
        if ($exists) {
            echo json_encode(['success' => false, 'message' => 'Już jesteś uczestnikiem']);
            exit;
        }
        
        // Dodaj uczestnika
        Database::query(
            "INSERT INTO EventParticipants (event_id, user_id) VALUES (?, ?)",
            [$eventId, $userId]
        );
        
        echo json_encode(['success' => true, 'message' => 'Dołączono do wydarzenia']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Błąd serwera']);
    }
    exit;
}

// DELETE - opuść wydarzenie
if ($method === 'DELETE') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Musisz być zalogowany']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = isset($data['event_id']) ? (int)$data['event_id'] : null;
    $userId = (int)$_SESSION['user_id'];
    
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Brak event_id']);
        exit;
    }
    
    try {
        Database::query(
            "DELETE FROM EventParticipants WHERE event_id = ? AND user_id = ?",
            [$eventId, $userId]
        );
        
        echo json_encode(['success' => true, 'message' => 'Opuszczono wydarzenie']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Błąd serwera']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Metoda niedozwolona']);
