<?php

/**
 * Klasa Database - Singleton do zarządzania połączeniem z bazą danych
 */
class Database
{
    private static ?PDO $instance = null;
    
    /**
     * Prywatny konstruktor - zapobiega bezpośredniemu tworzeniu obiektu
     *  NIE MOŻNA utworzyć obiektu przez new Database()
     */
    private function __construct() {}
    
    /**
     * Zapobiega klonowaniu instancji
     * NIE MOŻNA sklonować obiektu przez clone $db
     */
    private function __clone() {}
    
    /**
     * Zapobiega deserializacji instancji
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Pobierfa instancję PDO (Singleton)
     * 
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            require_once config('database.php');
            
            if (!isset($pdo)) {
                throw new Exception("Database connection not established");
            }
            
            self::$instance = $pdo;
        }
        
        return self::$instance;
    }
    
    /**
     * Testuje połączenie z bazą danych
     * 
     * @return array
     */
    public static function testConnection(): array
    {
        try {
            $pdo = self::getInstance();
            
            // Sprawdź połączenie
            $stmt = $pdo->query("SELECT 1");
            
            // Sprawdź czy baza EventApp istnieje
            $dbCheck = $pdo->query("SELECT DATABASE() as db_name");
            $dbName = $dbCheck->fetch()['db_name'];
            
            // Sprawdź ile tabel istnieje
            $tablesCheck = $pdo->query("SHOW TABLES");
            $tablesCount = $tablesCheck->rowCount();
            
            return [
                'success' => true,
                'message' => 'Połączenie z bazą danych działa poprawnie',
                'database' => $dbName,
                'tables_count' => $tablesCount
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Błąd połączenia z bazą danych',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Sprawdza czy wszystkie wymagane tabele istnieją
     * 
     * @return array
     */
    public static function checkTables(): array
    {
        $requiredTables = [
            'users',
            'categories',
            'subcategories',
            'userinterests',
            'events',
            'eventsubcategories',
            'eventparticipants',
            'eventratings',
            'friends',
            'userstats'
        ];
        
        try {
            $pdo = self::getInstance();
            
            $stmt = $pdo->query("SHOW TABLES");
            $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Porównanie bez względu na wielkość liter
            $existingTablesLower = array_map('strtolower', $existingTables);
            $missingTables = [];
            
            foreach ($requiredTables as $required) {
                if (!in_array(strtolower($required), $existingTablesLower)) {
                    $missingTables[] = $required;
                }
            }
            
            return [
                'success' => count($missingTables) === 0,
                'existing_tables' => $existingTables,
                'missing_tables' => array_values($missingTables),
                'total' => count($existingTables),
                'required' => count($requiredTables)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Wykonuje zapytanie SELECT i zwraca wszystkie wyniki
     * 
     * @param string $query Zapytanie SQL
     * @param array $params Parametry do zapytania
     * @return array|false
     */
    public static function query(string $query, array $params = []): array|false
    {
        try {
            $pdo = self::getInstance();
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Wykonuje zapytanie SELECT i zwraca jeden wynik
     * 
     * @param string $query Zapytanie SQL
     * @param array $params Parametry do zapytania
     * @return array|false|null
     */
    public static function queryOne(string $query, array $params = []): array|false|null
    {
        try {
            $pdo = self::getInstance();
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (PDOException $e) {
            error_log("Database queryOne error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Wykonuje zapytanie INSERT, UPDATE lub DELETE
     * 
     * @param string $query Zapytanie SQL
     * @param array $params Parametry do zapytania
     * @return bool
     */
    public static function execute(string $query, array $params = []): bool
    {
        try {
            $pdo = self::getInstance();
            $stmt = $pdo->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database execute error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Wykonuje INSERT i zwraca ID ostatnio wstawionego rekordu
     * 
     * @param string $query Zapytanie SQL INSERT
     * @param array $params Parametry do zapytania
     * @return string|false ID ostatnio wstawionego rekordu lub false w przypadku błędu
     */
    public static function insert(string $query, array $params = []): string|false
    {
        try {
            $pdo = self::getInstance();
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database insert error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rozpoczyna transakcję
     * 
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        try {
            return self::getInstance()->beginTransaction();
        } catch (PDOException $e) {
            error_log("Database beginTransaction error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Zatwierdza transakcję
     * 
     * @return bool
     */
    public static function commit(): bool
    {
        try {
            return self::getInstance()->commit();
        } catch (PDOException $e) {
            error_log("Database commit error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cofa transakcję
     * 
     * @return bool
     */
    public static function rollback(): bool
    {
        try {
            return self::getInstance()->rollBack();
        } catch (PDOException $e) {
            error_log("Database rollback error: " . $e->getMessage());
            return false;
        }
    }
}

