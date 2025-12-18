<?php
// EventsController - handles creating events
require_once SRC_PATH . '/classes/Database.php';
require_once SRC_PATH . '/classes/Security.php';

class EventsController
{
    /**
     * Create a new event from POST data
     * @return array
     */
    public function create(): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Nieprawidłowa metoda żądania', 'errors' => []];
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $title = isset($_POST['title']) ? Security::cleanInput((string)$_POST['title']) : '';
        $description = isset($_POST['description']) ? Security::cleanInput((string)$_POST['description']) : '';
        $date = isset($_POST['date']) ? Security::cleanInput((string)$_POST['date']) : '';
        $time = isset($_POST['time']) ? Security::cleanInput((string)$_POST['time']) : '';
        $category = isset($_POST['category']) ? Security::cleanInput((string)$_POST['category']) : '';
        $lat = isset($_POST['latitude']) ? $_POST['latitude'] : null;
        $lng = isset($_POST['longitude']) ? $_POST['longitude'] : null;

        $errors = [];

        if ($title === '') {
            $errors[] = 'Tytuł jest wymagany.';
        }
        if ($description === '') {
            $errors[] = 'Opis jest wymagany.';
        }

        // use provided date and time as-is (no rounding)
        $startDatetime = null;
        if ($date !== '' && $time !== '') {
            try {
                $dt = new DateTime($date . ' ' . $time);
                $startDatetime = $dt->format('Y-m-d H:i:00');
            } catch (Exception $e) {
                $startDatetime = null;
            }
        }

        if ($startDatetime === null) {
            $errors[] = 'Nieprawidłowa data lub godzina.';
        }

        // category required
        if (empty($category)) {
            $errors[] = 'Kategoria jest wymagana.';
        }

        // latitude / longitude required and numeric
        if ($lat === null || $lat === '') {
            $errors[] = 'Szerokość geograficzna jest wymagana.';
        } elseif (!is_numeric($lat)) {
            $errors[] = 'Nieprawidłowa szerokość geograficzna.';
        }

        if ($lng === null || $lng === '') {
            $errors[] = 'Długość geograficzna jest wymagana.';
        } elseif (!is_numeric($lng)) {
            $errors[] = 'Nieprawidłowa długość geograficzna.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => 'Walidacja nie przeszła', 'errors' => $errors];
        }

        $createdBy = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $latVal = is_numeric($lat) ? (float)$lat : null;
        $lngVal = is_numeric($lng) ? (float)$lng : null;

        // compute end_datetime = start + 30 minutes
        try {
            $dtStart = new DateTime($startDatetime);
            $dtEnd = clone $dtStart;
            $dtEnd->modify('+30 minutes');
            $endDatetime = $dtEnd->format('Y-m-d H:i:00');
        } catch (Exception $e) {
            $endDatetime = null;
        }

        $query = "INSERT INTO Events (title, description, latitude, longitude, start_datetime, end_datetime, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [$title, $description, $latVal, $lngVal, $startDatetime, $endDatetime, $createdBy];

        $insertId = Database::insert($query, $params);
        if ($insertId === false) {
            return ['success' => false, 'message' => 'Błąd zapisu do bazy danych.', 'errors' => ['Błąd zapisu do bazy danych.']];
        }

        return ['success' => true, 'message' => 'Wydarzenie zostało utworzone.', 'errors' => [], 'id' => $insertId];
    }
}
 