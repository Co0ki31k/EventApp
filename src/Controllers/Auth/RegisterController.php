<?php

/**
 * Controller rejestracji użytkownika
 */
class RegisterController
{
    private array $errors = [];
    private array $data = [];
    
    /**
     * Obsługuje proces rejestracji
     * 
     * @return array ['success' => bool, 'message' => string, 'errors' => array, 'user_id' => int|null]
     */
    public function register(): array
    {
        // Sprawdź czy to POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Nieprawidłowa metoda żądania',
                'errors' => [],
                'user_id' => null
            ];
        }
        
        // Weryfikacja tokenu CSRF
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!Security::verifyCsrfToken($csrfToken)) {
            return [
                'success' => false,
                'message' => 'Nieprawidłowy token CSRF. Odśwież stronę i spróbuj ponownie.',
                'errors' => ['csrf' => 'Nieprawidłowy token bezpieczeństwa'],
                'user_id' => null
            ];
        }
        
        // Sprawdź czy IP nie jest zablokowane
        if (Security::isIpBlocked('register')) {
            return [
                'success' => false,
                'message' => 'Twoje IP zostało tymczasowo zablokowane. Spróbuj ponownie później.',
                'errors' => ['blocked' => 'IP zablokowane'],
                'user_id' => null
            ];
        }
        
        // Rate Limiting - max 5 prób na 15 minut
        if (!Security::checkRateLimit('register', 5, 900)) {
            Security::blockIp('register', 30);
            return [
                'success' => false,
                'message' => 'Przekroczono limit prób rejestracji. Spróbuj ponownie za 30 minut.',
                'errors' => ['rate_limit' => 'Przekroczono limit prób'],
                'user_id' => null
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
                'user_id' => null
            ];
        }
        
        // Sprawdź czy email już istnieje
        if (User::emailExists($this->data['email'])) {
            $this->errors['email'] = 'Ten adres email jest już zarejestrowany';
            return [
                'success' => false,
                'message' => 'Email już istnieje w systemie',
                'errors' => $this->errors,
                'user_id' => null
            ];
        }
        
        // Sprawdź czy username już istnieje
        if (User::usernameExists($this->data['username'])) {
            $this->errors['username'] = 'Ta nazwa użytkownika jest już zajęta';
            return [
                'success' => false,
                'message' => 'Nazwa użytkownika jest już zajęta',
                'errors' => $this->errors,
                'user_id' => null
            ];
        }
        
        // Utwórz użytkownika
        $userId = User::create([
            'username' => $this->data['username'],
            'email' => $this->data['email'],
            'password' => $this->data['password'],
            'role' => $this->data['role'] ?? 'user'
        ]);
        
        if ($userId === false) {
            return [
                'success' => false,
                'message' => 'Błąd podczas tworzenia konta. Spróbuj ponownie.',
                'errors' => ['general' => 'Nie udało się utworzyć konta'],
                'user_id' => null
            ];
        }
        
        // Opcjonalnie: Wyślij email weryfikacyjny
        if (class_exists('EmailVerification')) {
            $token = EmailVerification::generateToken($userId);
            if ($token) {
                EmailVerification::sendVerificationEmail($this->data['email'], $token);
            }
        }
        
        // Resetuj rate limit po udanej rejestracji
        Security::resetRateLimit('register');
        
        // Sukces
        return [
            'success' => true,
            'message' => 'Konto zostało utworzone pomyślnie',
            'errors' => [],
            'user_id' => $userId
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
            'username' => isset($input['username']) ? trim(strip_tags($input['username'])) : '',
            'email' => isset($input['email']) ? trim(strtolower(filter_var($input['email'], FILTER_SANITIZE_EMAIL))) : '',
            'password' => isset($input['password']) ? $input['password'] : '',
            'role' => isset($input['role']) && in_array($input['role'], ['user', 'company']) ? $input['role'] : 'user'
        ];
    }
    
    /**
     * Waliduje dane wejściowe
     * 
     * @return bool
     */
    private function validateInput(): bool
    {
        // Walidacja username
        if (empty($this->data['username'])) {
            $this->errors['username'] = 'Nazwa użytkownika jest wymagana';
        } elseif (strlen($this->data['username']) < 3) {
            $this->errors['username'] = 'Nazwa użytkownika musi mieć minimum 3 znaki';
        } elseif (strlen($this->data['username']) > 50) {
            $this->errors['username'] = 'Nazwa użytkownika może mieć maksymalnie 50 znaków';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $this->data['username'])) {
            $this->errors['username'] = 'Nazwa użytkownika może zawierać tylko litery, cyfry i podkreślenia';
        }
        
        // Walidacja email
        if (empty($this->data['email'])) {
            $this->errors['email'] = 'Adres email jest wymagany';
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Nieprawidłowy format adresu email';
        } elseif (strlen($this->data['email']) > 100) {
            $this->errors['email'] = 'Adres email może mieć maksymalnie 100 znaków';
        }
        
        // Walidacja hasła
        if (empty($this->data['password'])) {
            $this->errors['password'] = 'Hasło jest wymagane';
        } elseif (strlen($this->data['password']) < 8) {
            $this->errors['password'] = 'Hasło musi mieć minimum 8 znaków';
        } elseif (!preg_match('/[A-Z]/', $this->data['password'])) {
            $this->errors['password'] = 'Hasło musi zawierać co najmniej jedną wielką literę';
        } elseif (!preg_match('/[a-z]/', $this->data['password'])) {
            $this->errors['password'] = 'Hasło musi zawierać co najmniej jedną małą literę';
        } elseif (!preg_match('/[0-9]/', $this->data['password'])) {
            $this->errors['password'] = 'Hasło musi zawierać co najmniej jedną cyfrę';
        } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $this->data['password'])) {
            $this->errors['password'] = 'Hasło musi zawierać co najmniej jeden znak specjalny (!@#$%^&*(),.?":{}|<>)';
        }
        
        
        // Walidacja roli
        if (!in_array($this->data['role'], ['user', 'company'])) {
            $this->errors['role'] = 'Nieprawidłowa rola użytkownika';
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
    
    /**
     * Sprawdza czy są błędy
     * 
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
