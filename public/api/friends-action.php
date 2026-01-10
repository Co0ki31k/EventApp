<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Tylko POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['action']) || !isset($input['friend_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$action = $input['action'];
$friendId = (int)$input['friend_id'];

try {
    
    switch ($action) {
        case 'accept':
            // Akceptuj zaproszenie - zmień status na accepted
            $result = Database::execute(
                "UPDATE Friends SET status = 'accepted' WHERE user_id = ? AND friend_id = ? AND status = 'pending'",
                [$friendId, $userId]
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Zaproszenie zaakceptowane']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie udało się zaakceptować zaproszenia']);
            }
            break;
            
        case 'reject':
            // Odrzuć zaproszenie - usuń wpis
            $result = Database::execute(
                "DELETE FROM Friends WHERE user_id = ? AND friend_id = ? AND status = 'pending'",
                [$friendId, $userId]
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Zaproszenie odrzucone']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie udało się odrzucić zaproszenia']);
            }
            break;
            
        case 'remove':
            // Usuń znajomego - usuń obie relacje
            Database::execute(
                "DELETE FROM Friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)",
                [$userId, $friendId, $friendId, $userId]
            );
            
            echo json_encode(['success' => true, 'message' => 'Znajomy usunięty']);
            break;
            
        case 'cancel':
            // Anuluj wysłane zaproszenie
            $result = Database::execute(
                "DELETE FROM Friends WHERE user_id = ? AND friend_id = ? AND status = 'pending'",
                [$userId, $friendId]
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Zaproszenie anulowane']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie udało się anulować zaproszenia']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
