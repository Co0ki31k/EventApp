<?php
/**
 * API endpoint - edycja podkategorii (tylko admin)
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

// Sprawdź metodę
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowa metoda']);
    exit;
}

// Pobierz dane z requestu
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['subcategory_id']) || !isset($data['name']) || trim($data['name']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID i nazwa podkategorii są wymagane']);
    exit;
}

$subcategoryId = (int)$data['subcategory_id'];
$name = trim($data['name']);
$description = isset($data['description']) ? trim($data['description']) : null;

try {
    // Sprawdź czy podkategoria istnieje i pobierz category_id
    $subcategory = Database::queryOne(
        "SELECT category_id FROM Subcategories WHERE subcategory_id = ?", 
        [$subcategoryId]
    );
    
    if (!$subcategory) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Podkategoria nie istnieje']);
        exit;
    }
    
    // Sprawdź czy inna podkategoria w tej samej kategorii nie ma już takiej nazwy
    $existing = Database::queryOne(
        "SELECT subcategory_id FROM Subcategories WHERE category_id = ? AND name = ? AND subcategory_id != ?", 
        [$subcategory['category_id'], $name, $subcategoryId]
    );
    
    if ($existing) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Podkategoria o takiej nazwie już istnieje w tej kategorii']);
        exit;
    }
    
    // Zaktualizuj podkategorię
    $updated = Database::execute(
        "UPDATE Subcategories SET name = ?, description = ? WHERE subcategory_id = ?",
        [$name, $description, $subcategoryId]
    );
    
    if ($updated !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Podkategoria została zaktualizowana'
        ]);
    } else {
        throw new Exception('Nie udało się zaktualizować podkategorii');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd: ' . $e->getMessage()]);
}
