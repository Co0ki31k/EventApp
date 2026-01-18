<?php
/**
 * API endpoint - usuwanie wydarzenia (tylko admin)
 */

header('Content-Type: application/json');

// Start session and check authentication
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';
require_once __DIR__ . '/../../src/classes/Security.php';

// Sprawdź czy użytkownik jest zalogowany i jest adminem
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Brak dostępu']);
    exit;
}

// Sprawdź metodę
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowa metoda']);
    exit;
}

// Pobierz dane z requestu
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['event_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Brak ID wydarzenia']);
    exit;
}

$eventId = (int)$data['event_id'];

try {
    // Sprawdź czy wydarzenie istnieje i jeszcze trwa
    $event = Database::queryOne("SELECT event_id, title, end_datetime FROM Events WHERE event_id = ?", [$eventId]);
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Wydarzenie nie istnieje']);
        exit;
    }
    
    // Sprawdź czy wydarzenie jeszcze nie zakończyło się
    $now = new DateTime();
    $endDate = new DateTime($event['end_datetime']);
    
    if ($endDate < $now) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nie można usunąć zakończonego wydarzenia']);
        exit;
    }
    
    // Usuń wydarzenie (CASCADE automatycznie usunie powiązane dane)
    $deleted = Database::execute("DELETE FROM Events WHERE event_id = ?", [$eventId]);
    
    if ($deleted) {
        echo json_encode([
            'success' => true,
            'message' => 'Wydarzenie zostało usunięte'
        ]);
    } else {
        throw new Exception('Nie udało się usunąć wydarzenia');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd: ' . $e->getMessage()]);
}
