<?php

/**
 * Klasa Security - podstawowe funkcje bezpieczeństwa
 */
class Security
{
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
}
