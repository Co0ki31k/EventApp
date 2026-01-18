<?php
/**
 * API endpoint - pobieranie i wyszukiwanie wydarzeń (tylko admin)
 */

header('Content-Type: application/json');

// Start session and check authentication
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../src/Helpers/url.php';
require_once __DIR__ . '/../../src/classes/Database.php';
require_once __DIR__ . '/../../src/classes/Security.php';

// Sprawdź czy użytkownik jest zalogowany i jest adminem
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Brak dostępu']);
    exit;
}

// Pobierz parametry wyszukiwania
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = ($page - 1) * $limit;

try {
    // Buduj zapytanie SQL
    $where = "1=1";
    $params = [];
    
    if (!empty($search)) {
        $where .= " AND (e.title LIKE ? OR c.name LIKE ? OR u.username LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Pobierz całkowitą liczbę wydarzeń
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM Events e
        LEFT JOIN Categories c ON e.category_id = c.category_id
        LEFT JOIN Users u ON e.created_by = u.user_id
        WHERE {$where}
    ";
    $countResult = Database::queryOne($countQuery, $params);
    $total = $countResult['total'] ?? 0;
    
    // Pobierz wydarzenia z paginacją
    $query = "
        SELECT 
            e.event_id,
            e.title,
            c.name as category,
            e.start_datetime,
            e.end_datetime,
            e.created_at,
            u.username as created_by_username,
            u.user_id as created_by_id
        FROM Events e
        LEFT JOIN Categories c ON e.category_id = c.category_id
        LEFT JOIN Users u ON e.created_by = u.user_id
        WHERE {$where}
        ORDER BY e.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $events = Database::query($query, $params);
    
    // Przygotuj odpowiedź
    $response = [
        'success' => true,
        'events' => $events ?: [],
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => (int)$total,
            'totalPages' => ceil($total / $limit)
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd serwera: ' . $e->getMessage()]);
}
