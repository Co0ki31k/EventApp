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
    // JOIN with Users table to get creator's role (user/company)
    $query = "SELECT e.event_id, e.title, e.description, e.latitude, e.longitude, 
                     e.start_datetime, e.end_datetime, e.created_by, e.created_at,
                     u.role AS creator_role, u.username AS creator_username
              FROM Events e
              LEFT JOIN Users u ON e.created_by = u.user_id
              WHERE e.latitude IS NOT NULL 
                AND e.longitude IS NOT NULL 
                AND (e.end_datetime >= NOW() OR e.end_datetime IS NULL)
              ORDER BY e.start_datetime ASC";
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
            'creator_role' => $r['creator_role'] ?? 'user',
            'creator_username' => $r['creator_username'] ?? null,
        ];
    }

    echo json_encode(['success' => true, 'events' => $events]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
