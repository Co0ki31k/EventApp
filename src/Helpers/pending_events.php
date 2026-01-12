<?php
/**
 * Pomocnicze funkcje do obsługi wydarzeń zapisanych przez gościa
 */

/**
 * Dołącza użytkownika do wszystkich wydarzeń zapisanych podczas trybu gościa
 * 
 * @param int $userId ID użytkownika
 * @return array ['success' => bool, 'joined' => int, 'errors' => int]
 */
function joinPendingEvents(int $userId): array
{
    if (!isset($_SESSION['guest_pending_events']) || empty($_SESSION['guest_pending_events'])) {
        return ['success' => true, 'joined' => 0, 'errors' => 0];
    }
    
    $pendingEvents = $_SESSION['guest_pending_events'];
    $joined = 0;
    $errors = 0;
    
    foreach ($pendingEvents as $eventId) {
        try {
            // Sprawdź czy wydarzenie istnieje
            $event = Database::queryOne(
                "SELECT event_id FROM Events WHERE event_id = ?",
                [$eventId]
            );
            
            if (!$event) {
                $errors++;
                continue;
            }
            
            // Sprawdź czy użytkownik już nie jest uczestnikiem
            $exists = Database::queryOne(
                "SELECT 1 FROM EventParticipants WHERE event_id = ? AND user_id = ?",
                [$eventId, $userId]
            );
            
            if ($exists) {
                $joined++; // Liczymy jako sukces
                continue;
            }
            
            // Dodaj użytkownika jako uczestnika
            Database::query(
                "INSERT INTO EventParticipants (event_id, user_id) VALUES (?, ?)",
                [$eventId, $userId]
            );
            
            $joined++;
        } catch (Exception $e) {
            error_log("Błąd dołączania do wydarzenia {$eventId}: " . $e->getMessage());
            $errors++;
        }
    }
    
    // Wyczyść zapisane wydarzenia z sesji
    unset($_SESSION['guest_pending_events']);
    
    return [
        'success' => $joined > 0 || $errors === 0,
        'joined' => $joined,
        'errors' => $errors
    ];
}

/**
 * Sprawdza czy są zapisane wydarzenia gościa w sesji
 * 
 * @return bool
 */
function hasPendingEvents(): bool
{
    return isset($_SESSION['guest_pending_events']) && !empty($_SESSION['guest_pending_events']);
}

/**
 * Pobiera liczbę zapisanych wydarzeń gościa
 * 
 * @return int
 */
function getPendingEventsCount(): int
{
    if (!isset($_SESSION['guest_pending_events'])) {
        return 0;
    }
    
    return count($_SESSION['guest_pending_events']);
}

/**
 * Pobiera szczegóły wydarzeń zapisanych przez gościa w sesji
 *
 * @return array
 */
function getPendingEventsDetails(): array
{
    if (!isset($_SESSION['guest_pending_events']) || empty($_SESSION['guest_pending_events'])) {
        return [];
    }
    $pendingEvents = $_SESSION['guest_pending_events'];
    $placeholders = implode(',', array_fill(0, count($pendingEvents), '?'));
    if (!$placeholders) return [];
    $events = Database::query(
        "SELECT event_id, title, start_datetime FROM Events WHERE event_id IN ($placeholders)",
        $pendingEvents
    );
    return $events ?: [];
}
