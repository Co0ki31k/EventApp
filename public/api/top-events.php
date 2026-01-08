<?php
// public/api/top-events.php
// Returns top 5 most popular events by participant count
header('Content-Type: application/json; charset=utf-8');

// Load paths and helpers first (needed by Database)
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';

try {
    // Query to get top 5 events by participant count (only essential fields)
    $query = "SELECT e.event_id, e.title, e.latitude, e.longitude, 
                     e.start_datetime,
                     c.name AS category_name,
                     COUNT(ep.user_id) AS participants_count
              FROM Events e
              LEFT JOIN Categories c ON e.category_id = c.category_id
              LEFT JOIN EventParticipants ep ON e.event_id = ep.event_id
              WHERE e.latitude IS NOT NULL 
                AND e.longitude IS NOT NULL 
                AND (e.end_datetime >= NOW() OR e.end_datetime IS NULL)
              GROUP BY e.event_id, e.title, e.latitude, e.longitude, 
                       e.start_datetime, c.name
              ORDER BY participants_count DESC, e.start_datetime ASC
              LIMIT 5";
    
    $rows = Database::query($query, []);

    if ($rows === false || !is_array($rows)) {
        $rows = [];
    }

    $events = [];
    foreach ($rows as $r) {
        $events[] = [
            'id' => isset($r['event_id']) ? (int)$r['event_id'] : null,
            'title' => $r['title'] ?? '',
            'category_name' => $r['category_name'] ?? null,
            'latitude' => $r['latitude'] ?? null,
            'longitude' => $r['longitude'] ?? null,
            'start_datetime' => $r['start_datetime'] ?? null,
            'participants_count' => isset($r['participants_count']) ? (int)$r['participants_count'] : 0,
        ];
    }

    echo json_encode(['success' => true, 'events' => $events]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
