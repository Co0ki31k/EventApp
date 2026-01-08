<?php
// public/api/event-details.php
// Returns detailed information about a specific event including participants and friend status
header('Content-Type: application/json; charset=utf-8');

// Load paths and helpers first (needed by Database)
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $currentUserId = (int)$_SESSION['user_id'];
    
    // Get event_id from request
    $eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
    
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Event ID is required']);
        exit;
    }
    
    // Query to get event details with creator info
    $eventQuery = "SELECT e.event_id, e.title, e.description, 
                          e.latitude, e.longitude, 
                          e.start_datetime, e.end_datetime,
                          e.created_by, u.username AS creator_username, u.role AS creator_role,
                          c.name AS category_name
                   FROM Events e
                   LEFT JOIN Users u ON e.created_by = u.user_id
                   LEFT JOIN Categories c ON e.category_id = c.category_id
                   WHERE e.event_id = ?";
    
    $eventRows = Database::query($eventQuery, [$eventId]);
    
    if (!$eventRows || count($eventRows) === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    $eventData = $eventRows[0];
    
    // Check if event is currently active
    $isActive = false;
    $startDatetime = $eventData['start_datetime'] ?? null;
    $endDatetime = $eventData['end_datetime'] ?? null;
    
    if ($startDatetime && $endDatetime) {
        $now = new DateTime();
        try {
            $startDate = new DateTime($startDatetime);
            $endDate = new DateTime($endDatetime);
            $isActive = ($startDate <= $now) && ($now <= $endDate);
        } catch (Exception $e) {
            $isActive = false;
        }
    }
    
    // Get list of all participants for this event
    $participantsQuery = "SELECT u.user_id, u.username, u.role, ep.joined_at
                          FROM EventParticipants ep
                          INNER JOIN Users u ON ep.user_id = u.user_id
                          WHERE ep.event_id = ?
                          ORDER BY ep.joined_at ASC";
    
    $participantsRows = Database::query($participantsQuery, [$eventId]);
    
    $participants = [];
    if ($participantsRows && is_array($participantsRows)) {
        // For each participant, check their friend status with current user
        foreach ($participantsRows as $p) {
            $participantId = isset($p['user_id']) ? (int)$p['user_id'] : null;
            
            // Check friend status between current user and this participant
            $friendStatus = null;
            if ($participantId && $participantId !== $currentUserId) {
                $friendQuery = "SELECT status FROM Friends 
                               WHERE (user_id = ? AND friend_id = ?) 
                                  OR (user_id = ? AND friend_id = ?)
                               LIMIT 1";
                
                $friendRows = Database::query($friendQuery, [
                    $currentUserId, $participantId,
                    $participantId, $currentUserId
                ]);
                
                if ($friendRows && is_array($friendRows) && count($friendRows) > 0) {
                    $friendStatus = $friendRows[0]['status'] ?? null;
                }
            }
            
            $participants[] = [
                'user_id' => $participantId,
                'username' => $p['username'] ?? '',
                'role' => $p['role'] ?? 'user',
                'joined_at' => $p['joined_at'] ?? null,
                'friend_status' => $friendStatus, // 'pending', 'accepted', or null
                'is_current_user' => ($participantId === $currentUserId),
            ];
        }
    }
    
    // Build response
    $response = [
        'success' => true,
        'event' => [
            'id' => isset($eventData['event_id']) ? (int)$eventData['event_id'] : null,
            'title' => $eventData['title'] ?? '',
            'description' => $eventData['description'] ?? '',
            'category_name' => $eventData['category_name'] ?? null,
            'latitude' => $eventData['latitude'] ?? null,
            'longitude' => $eventData['longitude'] ?? null,
            'start_datetime' => $eventData['start_datetime'] ?? null,
            'end_datetime' => $eventData['end_datetime'] ?? null,
            'is_active' => $isActive,
            'creator' => [
                'user_id' => isset($eventData['created_by']) ? (int)$eventData['created_by'] : null,
                'username' => $eventData['creator_username'] ?? '',
                'role' => $eventData['creator_role'] ?? 'user',
            ],
            'participants' => $participants,
            'participants_count' => count($participants),
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
