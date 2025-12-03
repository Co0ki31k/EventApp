<?php

/**
 * Klasa Security - obsługa zabezpieczeń aplikacji
 */
class Security
{
    /**
     * Generuje token CSRF i zapisuje w sesji
     * 
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Weryfikuje token CSRF
     * 
     * @param string|null $token
     * @return bool
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Token wygasa po 1 godzinie
        if (time() - $_SESSION['csrf_token_time'] > 3600) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }
        
        return $token !== null && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Pobiera token CSRF z sesji
     * 
     * @return string|null
     */
    public static function getCsrfToken(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['csrf_token'] ?? null;
    }
    
    /**
     * Rate Limiting - sprawdza liczbę prób rejestracji z danego IP
     * 
     * @param string $action Akcja (np. 'register', 'login')
     * @param int $maxAttempts Maksymalna liczba prób
     * @param int $timeWindow Okno czasowe w sekundach
     * @return bool True jeśli można kontynuować, false jeśli przekroczono limit
     */
    public static function checkRateLimit(string $action, int $maxAttempts = 5, int $timeWindow = 900): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $ip = self::getClientIp();
        $key = "rate_limit_{$action}_{$ip}";
        
        // Pobierz historię prób z sesji
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        // Usuń stare próby sprzed okna czasowego
        $currentTime = time();
        $_SESSION[$key] = array_filter($_SESSION[$key], function($timestamp) use ($currentTime, $timeWindow) {
            return ($currentTime - $timestamp) < $timeWindow;
        });
        
        // Sprawdź czy przekroczono limit
        if (count($_SESSION[$key]) >= $maxAttempts) {
            return false;
        }
        
        // Dodaj nową próbę
        $_SESSION[$key][] = $currentTime;
        
        return true;
    }
    
    /**
     * Pobiera rzeczywisty IP klienta
     * 
     * @return string
     */
    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Weź pierwszą wartość z listy IP
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Sanityzuje output - ochrona przed XSS
     * 
     * @param string $string
     * @return string
     */
    public static function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Sanityzuje tablicę - ochrona przed XSS
     * 
     * @param array $array
     * @return array
     */
    public static function escapeArray(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::escapeArray($value);
            } elseif (is_string($value)) {
                $result[$key] = self::escape($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    /**
     * Generuje bezpieczny losowy string
     * 
     * @param int $length
     * @return string
     */
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Hashuje hasło
     * 
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Weryfikuje hasło
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Sprawdza czy hasło wymaga ponownego zahashowania
     * 
     * @param string $hash
     * @return bool
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Czyści dane wejściowe
     * 
     * @param string $input
     * @return string
     */
    public static function cleanInput(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = strip_tags($input);
        return $input;
    }
    
    /**
     * Waliduje email
     * 
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Blokuje IP po przekroczeniu limitów
     * 
     * @param string $action
     * @param int $minutes Czas blokady w minutach
     * @return void
     */
    public static function blockIp(string $action, int $minutes = 30): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $ip = self::getClientIp();
        $key = "blocked_{$action}_{$ip}";
        $_SESSION[$key] = time() + ($minutes * 60);
    }
    
    /**
     * Sprawdza czy IP jest zablokowane
     * 
     * @param string $action
     * @return bool
     */
    public static function isIpBlocked(string $action): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $ip = self::getClientIp();
        $key = "blocked_{$action}_{$ip}";
        
        if (isset($_SESSION[$key])) {
            if (time() < $_SESSION[$key]) {
                return true;
            }
            unset($_SESSION[$key]);
        }
        
        return false;
    }
    
    /**
     * Resetuje limit prób dla danej akcji
     * 
     * @param string $action
     * @return void
     */
    public static function resetRateLimit(string $action): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $ip = self::getClientIp();
        $key = "rate_limit_{$action}_{$ip}";
        unset($_SESSION[$key]);
    }
}
