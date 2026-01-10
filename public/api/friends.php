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
    $friends = [];

    // 1. ZAPROSZENIA WYSŁANE PRZEZ UŻYTKOWNIKA (pending)
    $sentRequests = Database::query(
        "SELECT f.friend_id as user_id, u.username, f.requested_at, f.status
         FROM Friends f 
         JOIN Users u ON f.friend_id = u.user_id 
         WHERE f.user_id = ? AND f.status = 'pending' 
         ORDER BY f.requested_at DESC",
        [$userId]
    );

    if ($sentRequests !== false) {
        foreach ($sentRequests as $request) {
            $friends[] = [
                'user_id' => $request['user_id'],
                'username' => $request['username'],
                'status' => 'sent',
                'timestamp' => $request['requested_at']
            ];
        }
    }

    // 2. ZAPROSZENIA OTRZYMANE PRZEZ UŻYTKOWNIKA (pending)
    $receivedRequests = Database::query(
        "SELECT f.user_id, u.username, f.requested_at, f.status
         FROM Friends f 
         JOIN Users u ON f.user_id = u.user_id 
         WHERE f.friend_id = ? AND f.status = 'pending' 
         ORDER BY f.requested_at DESC",
        [$userId]
    );

    if ($receivedRequests !== false) {
        foreach ($receivedRequests as $request) {
            $friends[] = [
                'user_id' => $request['user_id'],
                'username' => $request['username'],
                'status' => 'received',
                'timestamp' => $request['requested_at']
            ];
        }
    }

    // 3. ZAAKCEPTOWANI ZNAJOMI (w obu kierunkach)
    $acceptedFriends = Database::query(
        "SELECT DISTINCT 
            CASE 
                WHEN f.user_id = ? THEN f.friend_id 
                ELSE f.user_id 
            END as user_id,
            u.username,
            f.requested_at
         FROM Friends f 
         JOIN Users u ON (
            CASE 
                WHEN f.user_id = ? THEN f.friend_id 
                ELSE f.user_id 
            END = u.user_id
         )
         WHERE (f.user_id = ? OR f.friend_id = ?) 
           AND f.status = 'accepted' 
         ORDER BY u.username ASC",
        [$userId, $userId, $userId, $userId]
    );

    if ($acceptedFriends !== false) {
        foreach ($acceptedFriends as $friend) {
            $friends[] = [
                'user_id' => $friend['user_id'],
                'username' => $friend['username'],
                'status' => 'accepted',
                'timestamp' => $friend['requested_at']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'friends' => $friends,
        'count' => count($friends)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
