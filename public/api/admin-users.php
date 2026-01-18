<?php
/**
 * API endpoint - pobieranie i wyszukiwanie użytkowników (tylko admin)
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
        $where .= " AND username LIKE ?";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
    }
    
    // Pobierz całkowitą liczbę użytkowników
    $countQuery = "SELECT COUNT(*) as total FROM Users WHERE {$where}";
    $countResult = Database::queryOne($countQuery, $params);
    $total = $countResult['total'] ?? 0;
    
    // Pobierz użytkowników z paginacją
    $query = "
        SELECT 
            user_id,
            username,
            email,
            role,
            last_login
        FROM Users
        WHERE {$where}
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $users = Database::query($query, $params);
    
    // Przygotuj odpowiedź
    $response = [
        'success' => true,
        'users' => $users ?: [],
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
    echo json_encode(['success' => false, 'error' => 'Błąd serwera']);
}
