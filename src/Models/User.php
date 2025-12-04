<?php

/**
 * Model User - zarządzanie użytkownikami
 */
class User
{
    /**
     * Sprawdza czy email już istnieje w bazie
     * 
     * @param string $email
     * @return bool
     */
    public static function emailExists(string $email): bool
    {
        $result = Database::queryOne(
            "SELECT user_id FROM Users WHERE email = ?",
            [$email]
        );
        
        return $result !== null && $result !== false;
    }
    
    /**
     * Sprawdza czy username już istnieje w bazie
     * 
     * @param string $username
     * @return bool
     */
    public static function usernameExists(string $username): bool
    {
        $result = Database::queryOne(
            "SELECT user_id FROM users WHERE username = ?",
            [$username]
        );
        
        return $result !== null && $result !== false;
    }
    
    /**
     * Tworzy nowego użytkownika
     * 
     * @param array $data Dane użytkownika (username, email, password, role)
     * @return string|false ID nowo utworzonego użytkownika lub false
     */
    public static function create(array $data): string|false
    {
        // Walidacja danych
        $validation = self::validate($data);
        if (!$validation['valid']) {
            return false;
        }
        
        // Sprawdź czy email już istnieje
        if (self::emailExists($data['email'])) {
            return false;
        }
        
        // Sprawdź czy username już istnieje
        if (self::usernameExists($data['username'])) {
            return false;
        }
        
        // Hashowanie hasła
        $hashedPassword = Security::hashPassword($data['password']);
        
        // Wstawienie do bazy
        $query = "INSERT INTO Users (username, email, password_hash, role, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        
        $params = [
            $data['username'],
            $data['email'],
            $hashedPassword,
            $data['role'] ?? 'user'
        ];
        
        return Database::insert($query, $params);
    }
    
    /**
     * Waliduje dane użytkownika
     * 
     * @param array $data
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validate(array $data): array
    {
        $errors = [];
        
        // Walidacja username
        if (empty($data['username'])) {
            $errors['username'] = 'Nazwa użytkownika jest wymagana';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Nazwa użytkownika musi mieć minimum 3 znaki';
        } elseif (strlen($data['username']) > 30) {
            $errors['username'] = 'Nazwa użytkownika może mieć maksymalnie 30 znaków';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Nazwa użytkownika może zawierać tylko litery, cyfry i podkreślenia';
        }
        
        // Walidacja email
        if (empty($data['email'])) {
            $errors['email'] = 'Email jest wymagany';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Nieprawidłowy format email';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'Email może mieć maksymalnie 100 znaków';
        }
        
        // Walidacja hasła - uproszczona
        if (empty($data['password'])) {
            $errors['password'] = 'Hasło jest wymagane';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Hasło musi mieć minimum 8 znaków';
        }
        
        // Walidacja roli
        if (isset($data['role']) && !in_array($data['role'], ['user', 'company'])) {
            $errors['role'] = 'Nieprawidłowa rola użytkownika';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Pobiera użytkownika po ID
     * 
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $result = Database::queryOne(
            "SELECT user_id, username, email, role, created_at FROM Users WHERE user_id = ?",
            [$id]
        );
        
        return $result === false ? null : $result;
    }
    
    /**
     * Pobiera użytkownika po email
     * 
     * @param string $email
     * @return array|null
     */
    public static function findByEmail(string $email): ?array
    {
        $result = Database::queryOne(
            "SELECT * FROM Users WHERE email = ?",
            [$email]
        );
        
        return $result === false ? null : $result;
    }
    
    /**
     * Pobiera użytkownika po username
     * 
     * @param string $username
     * @return array|null
     */
    public static function findByUsername(string $username): ?array
    {
        $result = Database::queryOne(
            "SELECT * FROM Users WHERE username = ?",
            [$username]
        );
        
        return $result === false ? null : $result;
    }
    
    /**
     * Aktualizuje dane użytkownika
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function update(int $id, array $data): bool
    {
        $allowedFields = ['username', 'email', 'bio', 'avatar_url'];
        $fields = [];
        $params = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $query = "UPDATE Users SET " . implode(', ', $fields) . " WHERE user_id = ?";
        
        return Database::execute($query, $params);
    }
    
    /**
     * Zmienia hasło użytkownika
     * 
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public static function changePassword(int $id, string $newPassword): bool
    {
        $hashedPassword = Security::hashPassword($newPassword);
        
        return Database::execute(
            "UPDATE Users SET password_hash = ? WHERE user_id = ?",
            [$hashedPassword, $id]
        );
    }
    
    /**
     * Usuwa użytkownika
     * 
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        return Database::execute(
            "DELETE FROM Users WHERE user_id = ?",
            [$id]
        );
    }
}
