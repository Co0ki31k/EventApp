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
    $notifications = [];

    // 1. ZAPROSZENIA DO ZNAJOMYCH - wysłane przez użytkownika (pending)
    $sentRequests = Database::query(
        "SELECT u.username, f.requested_at 
         FROM Friends f 
         JOIN Users u ON f.friend_id = u.user_id 
         WHERE f.user_id = ? AND f.status = 'pending' 
         ORDER BY f.requested_at DESC",
        [$userId]
    );

    if ($sentRequests !== false) {
        foreach ($sentRequests as $request) {
            $notifications[] = [
                'type' => 'friend_request_sent',
                'username' => $request['username'],
                'timestamp' => $request['requested_at']
            ];
        }
    }

    // 2. ZAPROSZENIA DO ZNAJOMYCH - otrzymane przez użytkownika (pending)
    $receivedRequests = Database::query(
        "SELECT u.username, f.requested_at 
         FROM Friends f 
         JOIN Users u ON f.user_id = u.user_id 
         WHERE f.friend_id = ? AND f.status = 'pending' 
         ORDER BY f.requested_at DESC",
        [$userId]
    );

    if ($receivedRequests !== false) {
        foreach ($receivedRequests as $request) {
            $notifications[] = [
                'type' => 'friend_request_received',
                'username' => $request['username'],
                'timestamp' => $request['requested_at']
            ];
        }
    }

    // 3. POMYŚLNE ZAPISANIE SIĘ DO WYDARZENIA (od ostatniego logowania)
    $eventJoins = Database::query(
        "SELECT e.title, ep.joined_at 
         FROM EventParticipants ep 
         JOIN Events e ON ep.event_id = e.event_id 
         JOIN Users u ON ep.user_id = u.user_id
         WHERE ep.user_id = ? 
         AND ep.joined_at >= u.last_login
         AND (e.end_datetime IS NULL OR e.end_datetime >= NOW())
         ORDER BY ep.joined_at DESC",
        [$userId]
    );

    if ($eventJoins !== false) {
        foreach ($eventJoins as $join) {
            $notifications[] = [
                'type' => 'event_joined',
                'event_title' => $join['title'],
                'timestamp' => $join['joined_at']
            ];
        }
    }

    // 4. UTWORZONE WYDARZENIA PRZEZ UŻYTKOWNIKA (od ostatniego logowania)
    $createdEvents = Database::query(
        "SELECT e.title, e.created_at 
         FROM Events e 
         JOIN Users u ON e.created_by = u.user_id
         WHERE e.created_by = ? 
         AND e.created_at >= COALESCE(u.last_login, DATE_SUB(NOW(), INTERVAL 7 DAY))
         AND (e.end_datetime IS NULL OR e.end_datetime >= NOW())
         ORDER BY e.created_at DESC",
        [$userId]
    );

    if ($createdEvents !== false) {
        foreach ($createdEvents as $event) {
            $notifications[] = [
                'type' => 'event_created',
                'event_title' => $event['title'],
                'timestamp' => $event['created_at']
            ];
        }
    }

    // 5. WYDARZENIA NADCHODZĄCE (w ciągu najbliższych 15 minut)
    $upcomingEvents = Database::query(
        "SELECT e.title, e.start_datetime, 
                TIMESTAMPDIFF(MINUTE, NOW(), e.start_datetime) as minutes_until 
         FROM EventParticipants ep 
         JOIN Events e ON ep.event_id = e.event_id 
         WHERE ep.user_id = ? 
         AND e.start_datetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
         AND e.start_datetime > NOW() 
         ORDER BY e.start_datetime ASC",
        [$userId]
    );

    if ($upcomingEvents !== false) {
        foreach ($upcomingEvents as $event) {
            $notifications[] = [
                'type' => 'event_starting_soon',
                'event_title' => $event['title'],
                'start_datetime' => $event['start_datetime'],
                'minutes_until' => $event['minutes_until'],
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }

    // 6. WYDARZENIA TRWAJĄCE TERAZ
    $ongoingEvents = Database::query(
        "SELECT e.title, e.start_datetime, e.end_datetime,
                TIMESTAMPDIFF(MINUTE, NOW(), e.end_datetime) as minutes_remaining
         FROM EventParticipants ep 
         JOIN Events e ON ep.event_id = e.event_id 
         WHERE ep.user_id = ? 
         AND NOW() BETWEEN e.start_datetime AND e.end_datetime
         ORDER BY e.end_datetime ASC",
        [$userId]
    );

    if ($ongoingEvents !== false) {
        foreach ($ongoingEvents as $event) {
            $notifications[] = [
                'type' => 'event_ongoing',
                'event_title' => $event['title'],
                'start_datetime' => $event['start_datetime'],
                'end_datetime' => $event['end_datetime'],
                'minutes_remaining' => $event['minutes_remaining'],
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }

    // Sortuj wszystkie powiadomienia według czasu (najnowsze na górze)
    usort($notifications, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'count' => count($notifications)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
