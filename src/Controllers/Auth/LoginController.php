<?php

/**
 * LoginController - obsługa logowania użytkowników
 */
class LoginController
{
    private array $errors = [];
    private array $data = [];
    
    /**
     * Obsługuje proces logowania
     * 
     * @return array ['success' => bool, 'message' => string, 'errors' => array, 'user' => array|null]
     */
    public function login(): array
    {
        // Sprawdź czy to POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Nieprawidłowa metoda żądania',
                'errors' => [],
                'user' => null
            ];
        }
        
        // Pobierz i sanityzuj dane
        $this->data = $this->sanitizeInput($_POST);
        
        // Walidacja po stronie serwera
        if (!$this->validateInput()) {
            return [
                'success' => false,
                'message' => 'Dane formularza zawierają błędy',
                'errors' => $this->errors,
                'user' => null
            ];
        }
        
        // Sprawdź czy użytkownik istnieje
        $user = User::findByEmail($this->data['email']);
        
        if (!$user) {
            $this->errors['email'] = 'Nieprawidłowy email lub hasło';
            return [
                'success' => false,
                'message' => 'Nieprawidłowy email lub hasło',
                'errors' => $this->errors,
                'user' => null
            ];
        }
        
        // Sprawdź hasło
        if (!password_verify($this->data['password'], $user['password_hash'])) {
            $this->errors['password'] = 'Nieprawidłowy email lub hasło';
            return [
                'success' => false,
                'message' => 'Nieprawidłowy email lub hasło',
                'errors' => $this->errors,
                'user' => null
            ];
        }
        
        // Sprawdź czy konto jest aktywne
        if (!$user['is_active']) {
            $this->errors['general'] = 'Twoje konto nie zostało jeszcze aktywowane. Dokończ proces rejestracji.';
            return [
                'success' => false,
                'message' => 'Konto nieaktywne',
                'errors' => $this->errors,
                'user' => null
            ];
        }
        
        // Sukces - zwróć dane użytkownika (bez hasła)
        unset($user['password_hash']);
        
        return [
            'success' => true,
            'message' => 'Zalogowano pomyślnie',
            'errors' => [],
            'user' => $user
        ];
    }
    
    /**
     * Sanityzuje dane wejściowe
     * 
     * @param array $input
     * @return array
     */
    private function sanitizeInput(array $input): array
    {
        return [
            'email' => isset($input['email']) ? trim(strtolower(filter_var($input['email'], FILTER_SANITIZE_EMAIL))) : '',
            'password' => isset($input['password']) ? $input['password'] : ''
        ];
    }
    
    /**
     * Waliduje dane wejściowe
     * 
     * @return bool
     */
    private function validateInput(): bool
    {
        // Walidacja email
        if (empty($this->data['email'])) {
            $this->errors['email'] = 'Adres email jest wymagany';
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Nieprawidłowy format adresu email';
        }
        
        // Walidacja hasła
        if (empty($this->data['password'])) {
            $this->errors['password'] = 'Hasło jest wymagane';
        }
        
        return empty($this->errors);
    }
    
    /**
     * Pobiera błędy walidacji
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Pobiera sanityzowane dane
     * 
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
