<?php
header('Content-Type: application/json');

// Load paths and helpers first (needed by Database)
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metoda niedozwolona'
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Musisz być zalogowany'
    ]);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['user_id']) || empty($data['user_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Brak ID użytkownika'
    ]);
    exit;
}

$currentUserId = $_SESSION['user_id'];
$friendId = intval($data['user_id']);

// Can't send friend request to yourself
if ($currentUserId === $friendId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Nie możesz wysłać zaproszenia do siebie'
    ]);
    exit;
}

try {
    // Check if friendship already exists (in either direction)
    $checkQuery = "SELECT status, user_id, friend_id 
                   FROM Friends 
                   WHERE (user_id = ? AND friend_id = ?) 
                      OR (user_id = ? AND friend_id = ?)";
    
    $existing = Database::queryOne($checkQuery, [$currentUserId, $friendId, $friendId, $currentUserId]);
    
    if ($existing) {
        if ($existing['status'] === 'accepted') {
            echo json_encode([
                'success' => false,
                'message' => 'Jesteście już znajomymi'
            ]);
            exit;
        } else if ($existing['status'] === 'pending') {
            // Check who sent the original request
            if ($existing['user_id'] === $currentUserId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Zaproszenie już zostało wysłane'
                ]);
            } else {
                // The other person already sent a request to us
                echo json_encode([
                    'success' => false,
                    'message' => 'Ten użytkownik już wysłał Ci zaproszenie. Sprawdź swoje zaproszenia.'
                ]);
            }
            exit;
        }
    }

    // Insert new friend request
    $insertQuery = "INSERT INTO Friends (user_id, friend_id, status, requested_at) 
                    VALUES (?, ?, 'pending', NOW())";
    
    $result = Database::execute($insertQuery, [$currentUserId, $friendId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Zaproszenie zostało wysłane'
        ]);
    } else {
        throw new Exception('Nie udało się wysłać zaproszenia');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Wystąpił błąd: ' . $e->getMessage()
    ]);
}
