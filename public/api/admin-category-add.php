<?php
/**
 * API endpoint - dodawanie kategorii (tylko admin)
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

if (!isset($data['name']) || trim($data['name']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nazwa kategorii jest wymagana']);
    exit;
}

$name = trim($data['name']);
$description = isset($data['description']) ? trim($data['description']) : null;

try {
    // Sprawdź czy kategoria o takiej nazwie już istnieje
    $existing = Database::queryOne("SELECT category_id FROM Categories WHERE name = ?", [$name]);
    
    if ($existing) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Kategoria o takiej nazwie już istnieje']);
        exit;
    }
    
    // Dodaj kategorię
    $inserted = Database::execute(
        "INSERT INTO Categories (name, description) VALUES (?, ?)",
        [$name, $description]
    );
    
    if ($inserted) {
        echo json_encode([
            'success' => true,
            'message' => 'Kategoria została dodana'
        ]);
    } else {
        throw new Exception('Nie udało się dodać kategorii');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd: ' . $e->getMessage()]);
}
