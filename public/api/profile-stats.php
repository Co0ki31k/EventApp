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

$userId = (int)$_SESSION['user_id'];

try {
    // Pobierz dane użytkownika
    $user = Database::queryOne(
        "SELECT username, email FROM Users WHERE user_id = ?",
        [$userId]
    );
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Liczba wydarzeń w których użytkownik brał udział
    $eventsCount = Database::queryOne(
        "SELECT COUNT(DISTINCT event_id) as count 
         FROM EventParticipants 
         WHERE user_id = ?",
        [$userId]
    );
    
    // Liczba znajomych (zaakceptowanych)
    $friendsCount = Database::queryOne(
        "SELECT COUNT(DISTINCT 
            CASE 
                WHEN user_id = ? THEN friend_id 
                ELSE user_id 
            END
         ) as count 
         FROM Friends 
         WHERE (user_id = ? OR friend_id = ?) AND status = 'accepted'",
        [$userId, $userId, $userId]
    );
    
    echo json_encode([
        'success' => true,
        'username' => $user['username'],
        'email' => $user['email'],
        'events_count' => (int)$eventsCount['count'],
        'friends_count' => (int)$friendsCount['count']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
