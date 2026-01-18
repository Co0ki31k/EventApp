<?php
/**
 * API endpoint - usuwanie podkategorii (tylko admin)
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

if (!isset($data['subcategory_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Brak ID podkategorii']);
    exit;
}

$subcategoryId = (int)$data['subcategory_id'];

try {
    // Sprawdź czy podkategoria istnieje
    $subcategory = Database::queryOne("SELECT name FROM Subcategories WHERE subcategory_id = ?", [$subcategoryId]);
    
    if (!$subcategory) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Podkategoria nie istnieje']);
        exit;
    }
    
    // Usuń podkategorię (CASCADE automatycznie usunie powiązania)
    $deleted = Database::execute("DELETE FROM Subcategories WHERE subcategory_id = ?", [$subcategoryId]);
    
    if ($deleted) {
        echo json_encode([
            'success' => true,
            'message' => 'Podkategoria została usunięta'
        ]);
    } else {
        throw new Exception('Nie udało się usunąć podkategorii');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd: ' . $e->getMessage()]);
}
