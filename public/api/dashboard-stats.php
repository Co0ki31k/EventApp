<?php
/**
 * API endpoint for dashboard statistics
 * Returns all necessary data for admin dashboard tiles
 */

// Set JSON header
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

try {
    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Unauthorized access'
        ]);
        exit;
    }

    // Current date for filtering
    $today = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');
    
    // ==========================================
    // WYDARZENIA (Events)
    // ==========================================
    
    // Total events count
    $eventsTotal = Database::queryOne(
        "SELECT COUNT(*) as total FROM Events"
    );
    $eventsTotal = (int)($eventsTotal['total'] ?? 0);
    
    // Events this month
    $eventsMonth = Database::queryOne(
        "SELECT COUNT(*) as total 
         FROM Events 
         WHERE created_at >= ? 
         AND created_at <= ?",
        [
            $firstDayOfMonth . ' 00:00:00',
            $lastDayOfMonth . ' 23:59:59'
        ]
    );
    $eventsMonth = (int)($eventsMonth['total'] ?? 0);
    
    // Events today
    $eventsToday = Database::queryOne(
        "SELECT COUNT(*) as total 
         FROM Events 
         WHERE DATE(created_at) = ?",
        [$today]
    );
    $eventsToday = (int)($eventsToday['total'] ?? 0);
    
    // Events in last 12 months (month by month)
    $eventsYearRows = Database::query(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count
         FROM Events
         WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
         GROUP BY period
         ORDER BY period ASC"
    );
    $eventsYearRows = is_array($eventsYearRows) ? $eventsYearRows : [];

    // Events in last 7 days (day by day)
    $eventsWeekRows = Database::query(
        "SELECT DATE(created_at) as day, COUNT(*) as count
         FROM Events
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY day
         ORDER BY day ASC"
    );
    $eventsWeekRows = is_array($eventsWeekRows) ? $eventsWeekRows : [];

    // Events in current month (day by day)
    $eventsMonthRows = Database::query(
        "SELECT DATE(created_at) as day, COUNT(*) as count
         FROM Events
         WHERE created_at >= ? AND created_at <= ?
         GROUP BY day
         ORDER BY day ASC",
        [$firstDayOfMonth . ' 00:00:00', $lastDayOfMonth . ' 23:59:59']
    );
    $eventsMonthRows = is_array($eventsMonthRows) ? $eventsMonthRows : [];

    // Events today and yesterday
    $eventsTodayRow = Database::queryOne(
        "SELECT COUNT(*) as total FROM Events WHERE DATE(created_at) = ?",
        [$today]
    );
    $eventsToday = (int)($eventsTodayRow['total'] ?? 0);

    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $eventsYesterdayRow = Database::queryOne(
        "SELECT COUNT(*) as total FROM Events WHERE DATE(created_at) = ?",
        [$yesterday]
    );
    $eventsYesterday = (int)($eventsYesterdayRow['total'] ?? 0);
    
    // ==========================================
    // UCZESTNICY (Participants)
    // ==========================================
    
    // Total participants count (unique users who joined events)
    $participantsTotal = Database::queryOne(
        "SELECT COUNT(DISTINCT user_id) as total FROM EventParticipants"
    );
    $participantsTotal = (int)($participantsTotal['total'] ?? 0);
    
    // Participants this month
    $participantsMonth = Database::queryOne(
        "SELECT COUNT(DISTINCT user_id) as total 
         FROM EventParticipants 
         WHERE joined_at >= ? 
         AND joined_at <= ?",
        [
            $firstDayOfMonth . ' 00:00:00',
            $lastDayOfMonth . ' 23:59:59'
        ]
    );
    $participantsMonth = (int)($participantsMonth['total'] ?? 0);
    
    // Participants today
    $participantsToday = Database::queryOne(
        "SELECT COUNT(DISTINCT user_id) as total 
         FROM EventParticipants 
         WHERE DATE(joined_at) = ?",
        [$today]
    );
    $participantsToday = (int)($participantsToday['total'] ?? 0);
    
    // Participants in last 12 months (month by month)
    $participantsYearRows = Database::query(
        "SELECT DATE_FORMAT(joined_at, '%Y-%m') as period, COUNT(DISTINCT user_id) as count
         FROM EventParticipants
         WHERE joined_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
         GROUP BY period
         ORDER BY period ASC"
    );
    $participantsYearRows = is_array($participantsYearRows) ? $participantsYearRows : [];

    // Participants in last 7 days (day by day)
    $participantsWeekRows = Database::query(
        "SELECT DATE(joined_at) as day, COUNT(DISTINCT user_id) as count
         FROM EventParticipants
         WHERE joined_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY day
         ORDER BY day ASC"
    );
    $participantsWeekRows = is_array($participantsWeekRows) ? $participantsWeekRows : [];

    // Participants in current month (day by day)
    $participantsMonthRows = Database::query(
        "SELECT DATE(joined_at) as day, COUNT(DISTINCT user_id) as count
         FROM EventParticipants
         WHERE joined_at >= ? AND joined_at <= ?
         GROUP BY day
         ORDER BY day ASC",
        [$firstDayOfMonth . ' 00:00:00', $lastDayOfMonth . ' 23:59:59']
    );
    $participantsMonthRows = is_array($participantsMonthRows) ? $participantsMonthRows : [];

    // Participants today and yesterday
    $participantsTodayRow = Database::queryOne(
        "SELECT COUNT(DISTINCT user_id) as total FROM EventParticipants WHERE DATE(joined_at) = ?",
        [$today]
    );
    $participantsToday = (int)($participantsTodayRow['total'] ?? 0);

    $participantsYesterdayRow = Database::queryOne(
        "SELECT COUNT(DISTINCT user_id) as total FROM EventParticipants WHERE DATE(joined_at) = ?",
        [$yesterday]
    );
    $participantsYesterday = (int)($participantsYesterdayRow['total'] ?? 0);
    
    // ==========================================
    // Prepare series (fill missing months/days with zeros)
    // ==========================================

    // Map DB rows for quick lookup
    $eventsYearMap = [];
    foreach ($eventsYearRows as $r) {
        $eventsYearMap[$r['period']] = (int)$r['count'];
    }

    $participantsYearMap = [];
    foreach ($participantsYearRows as $r) {
        $participantsYearMap[$r['period']] = (int)$r['count'];
    }

    $eventsDayMap = [];
    foreach ($eventsWeekRows as $r) {
        $eventsDayMap[$r['day']] = (int)$r['count'];
    }

    // map for month days
    $eventsMonthMap = [];
    foreach ($eventsMonthRows as $r) {
        $eventsMonthMap[$r['day']] = (int)$r['count'];
    }

    $participantsDayMap = [];
    foreach ($participantsWeekRows as $r) {
        $participantsDayMap[$r['day']] = (int)$r['count'];
    }

    // map for month days
    $participantsMonthMap = [];
    foreach ($participantsMonthRows as $r) {
        $participantsMonthMap[$r['day']] = (int)$r['count'];
    }

    // Build last 12 months series (oldest -> newest)
    $events_year = [];
    $participants_year = [];
    for ($i = 11; $i >= 0; $i--) {
        $period = date('Y-m', strtotime("-{$i} months"));
        $events_year[] = ['period' => $period, 'count' => $eventsYearMap[$period] ?? 0];
        $participants_year[] = ['period' => $period, 'count' => $participantsYearMap[$period] ?? 0];
    }

    // Build last 7 days series (oldest -> newest)
    $events_week = [];
    $participants_week = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-{$i} days"));
        $events_week[] = ['date' => $day, 'count' => $eventsDayMap[$day] ?? 0];
        $participants_week[] = ['date' => $day, 'count' => $participantsDayMap[$day] ?? 0];
    }

    // Build current month series (day by day, oldest -> newest)
    $events_month_series = [];
    $participants_month_series = [];
    $start = new DateTime($firstDayOfMonth);
    $end = new DateTime($lastDayOfMonth);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end->modify('+1 day'));
    foreach ($period as $dt) {
        $d = $dt->format('Y-m-d');
        $events_month_series[] = ['date' => $d, 'count' => $eventsMonthMap[$d] ?? 0];
        $participants_month_series[] = ['date' => $d, 'count' => $participantsMonthMap[$d] ?? 0];
    }

    // ==========================================
    // RESPONSE
    // ==========================================
    $response = [
        'success' => true,
        'data' => [
            'events_total' => $eventsTotal,
            'events_month' => $eventsMonth,
            'events_today' => $eventsToday,
            'events_yesterday' => $eventsYesterday,
            'events_week' => $events_week,
            'events_month_series' => $events_month_series,
            'events_year' => $events_year,
            'participants_total' => $participantsTotal,
            'participants_month' => $participantsMonth,
            'participants_today' => $participantsToday,
            'participants_yesterday' => $participantsYesterday,
            'participants_week' => $participants_week,
            'participants_month_series' => $participants_month_series,
            'participants_year' => $participants_year,
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
