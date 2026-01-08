<?php
// public/api/my-events.php
// Returns events that the current user is participating in
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

    $userId = (int)$_SESSION['user_id'];
    
    // Query to get all events that user is participating in
    $query = "SELECT e.event_id, e.title, e.latitude, e.longitude, 
                     e.start_datetime, e.end_datetime,
                     c.name AS category_name
              FROM Events e
              INNER JOIN EventParticipants ep ON e.event_id = ep.event_id
              LEFT JOIN Categories c ON e.category_id = c.category_id
              WHERE ep.user_id = ?
                AND e.latitude IS NOT NULL 
                AND e.longitude IS NOT NULL 
                AND (e.end_datetime >= NOW() OR e.end_datetime IS NULL)
              ORDER BY e.start_datetime ASC";
    
    $rows = Database::query($query, [$userId]);

    if ($rows === false || !is_array($rows)) {
        $rows = [];
    }

    $events = [];
    $now = new DateTime();
    
    foreach ($rows as $r) {
        $startDatetime = $r['start_datetime'] ?? null;
        $endDatetime = $r['end_datetime'] ?? null;
        $isActive = false;
        
        if ($startDatetime && $endDatetime) {
            try {
                $startDate = new DateTime($startDatetime);
                $endDate = new DateTime($endDatetime);
                // Event is active if NOW is between start and end
                $isActive = ($startDate <= $now) && ($now <= $endDate);
            } catch (Exception $e) {
                $isActive = false;
            }
        }
        
        $events[] = [
            'id' => isset($r['event_id']) ? (int)$r['event_id'] : null,
            'title' => $r['title'] ?? '',
            'category_name' => $r['category_name'] ?? null,
            'latitude' => $r['latitude'] ?? null,
            'longitude' => $r['longitude'] ?? null,
            'start_datetime' => $startDatetime,
            'is_active' => $isActive,
        ];
    }

    echo json_encode(['success' => true, 'events' => $events]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
