<?php
/**
 * API endpoint - zmiana roli użytkownika (tylko admin)
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

if (!isset($data['user_id']) || !isset($data['new_role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych']);
    exit;
}

$userId = (int)$data['user_id'];
$newRole = $data['new_role'];

// Walidacja roli - tylko user i admin
$allowedRoles = ['user', 'admin'];
if (!in_array($newRole, $allowedRoles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowa rola']);
    exit;
}

// Sprawdź czy użytkownik nie próbuje zmienić własnej roli
if ($userId === $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nie możesz zmienić własnej roli']);
    exit;
}

try {
    // Sprawdź czy użytkownik istnieje
    $user = Database::queryOne("SELECT user_id, username, role FROM Users WHERE user_id = ?", [$userId]);
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Użytkownik nie istnieje']);
        exit;
    }
    
    // Zaktualizuj rolę
    $query = "UPDATE Users SET role = ? WHERE user_id = ?";
    $result = Database::execute($query, [$newRole, $userId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Rola użytkownika została zmieniona',
            'user' => [
                'user_id' => $userId,
                'username' => $user['username'],
                'old_role' => $user['role'],
                'new_role' => $newRole
            ]
        ]);
    } else {
        throw new Exception('Nie udało się zaktualizować roli');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd serwera: ' . $e->getMessage()]);
}
