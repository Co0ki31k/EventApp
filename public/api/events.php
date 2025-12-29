<?php
// public/api/events.php
// Simple JSON endpoint returning events that have coordinates
header('Content-Type: application/json; charset=utf-8');

// Load paths and helpers first (needed by Database)
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';

try {
    // Only events that are currently ongoing or upcoming (not ended yet)
    // end_datetime >= NOW() OR end_datetime IS NULL (no end time set)
    $query = "SELECT event_id, title, description, latitude, longitude, start_datetime, end_datetime, created_by, created_at 
              FROM Events 
              WHERE latitude IS NOT NULL 
                AND longitude IS NOT NULL 
                AND (end_datetime >= NOW() OR end_datetime IS NULL)
              ORDER BY start_datetime ASC";
    $rows = Database::query($query, []);

    if ($rows === false || !is_array($rows)) {
        $rows = [];
    }

    $events = [];
    foreach ($rows as $r) {
        $events[] = [
            'id' => isset($r['event_id']) ? (int)$r['event_id'] : null,
            'title' => $r['title'] ?? '',
            'description' => $r['description'] ?? '',
            'latitude' => $r['latitude'] ?? null,
            'longitude' => $r['longitude'] ?? null,
            'start_datetime' => $r['start_datetime'] ?? null,
            'end_datetime' => $r['end_datetime'] ?? null,
            'created_by' => isset($r['created_by']) ? (int)$r['created_by'] : null,
        ];
    }

    echo json_encode(['success' => true, 'events' => $events]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
