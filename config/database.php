<?php
$host = "localhost";
$db   = "EventApp";
$user = "root";
$pass = "";

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // rzuca wyjątki → łatwiejsze debugowanie
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // fetch() zwróci tablice asocjacyjną
    PDO::ATTR_EMULATE_PREPARES   => false,                   // prawdziwe prepared statements (bezpieczeństwo)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {

    // NIE pokazujemy prawdziwego błędu użytkownikowi
    // zapisujemy wyjątek do logu
    error_log("Database connection error: " . $e->getMessage());

    die("Wystąpił błąd połączenia z serwerem.");
}
?>
