<?php
/**
 * API endpoint - zarządzanie kategoriami i podkategoriami (tylko admin)
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

try {
    // Pobierz wszystkie kategorie z liczbą podkategorii
    $categories = Database::query("
        SELECT 
            c.category_id,
            c.name,
            c.description,
            c.created_at,
            COUNT(s.subcategory_id) as subcategories_count
        FROM Categories c
        LEFT JOIN Subcategories s ON c.category_id = s.category_id
        GROUP BY c.category_id
        ORDER BY c.name ASC
    ");

    // Dla każdej kategorii pobierz podkategorie
    foreach ($categories as &$category) {
        $category['subcategories'] = Database::query("
            SELECT 
                subcategory_id,
                name,
                description,
                created_at
            FROM Subcategories
            WHERE category_id = ?
            ORDER BY name ASC
        ", [$category['category_id']]);
    }

    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd: ' . $e->getMessage()]);
}
