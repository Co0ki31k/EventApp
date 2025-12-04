<?php

/**
 * Klasa EmailVerification - obsługa weryfikacji email
 */
class EmailVerification
{
    /**
     * Generuje token weryfikacyjny dla użytkownika
     * 
     * @param int $userId
     * @return string|false Token lub false w przypadku błędu
     */
    public static function generateToken(int $userId): string|false
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $query = "INSERT INTO EmailVerifications (user_id, token, expires_at, created_at) 
                  VALUES (?, ?, ?, NOW())";
        
        $result = Database::execute($query, [$userId, $token, $expiresAt]);
        
        return $result ? $token : false;
    }
    
    /**
     * Weryfikuje token
     * 
     * @param string $token
     * @return array|null Dane użytkownika lub null
     */
    public static function verifyToken(string $token): ?array
    {
        $query = "SELECT ev.*, u.user_id, u.email 
                  FROM EmailVerifications ev
                  JOIN Users u ON ev.user_id = u.user_id
                  WHERE ev.token = ? 
                  AND ev.expires_at > NOW() 
                  AND ev.verified_at IS NULL
                  LIMIT 1";
        
        $result = Database::queryOne($query, [$token]);
        
        return $result === false ? null : $result;
    }
    
    /**
     * Oznacza email jako zweryfikowany
     * 
     * @param string $token
     * @return bool
     */
    public static function markAsVerified(string $token): bool
    {
        Database::beginTransaction();
        
        try {
            // Pobierz dane weryfikacji
            $verification = self::verifyToken($token);
            
            if ($verification === null) {
                Database::rollback();
                return false;
            }
            
            // Zaktualizuj tabelę weryfikacji
            $query1 = "UPDATE EmailVerifications 
                       SET verified_at = NOW() 
                       WHERE token = ?";
            
            Database::execute($query1, [$token]);
            
            // Zaktualizuj użytkownika
            $query2 = "UPDATE Users 
                       SET email_verified_at = NOW() 
                       WHERE user_id = ?";
            
            Database::execute($query2, [$verification['user_id']]);
            
            Database::commit();
            return true;
            
        } catch (Exception $e) {
            Database::rollback();
            error_log("Email verification error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Wysyła email z linkiem weryfikacyjnym
     * 
     * @param string $email
     * @param string $token
     * @return bool
     */
    public static function sendVerificationEmail(string $email, string $token): bool
    {
        // W środowisku deweloperskim tylko loguj, nie wysyłaj
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $verificationUrl = url("verify-email.php?token=" . urlencode($token));
            
            // Zapisz do sesji dla wyświetlenia użytkownikowi
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['verification_link'] = $verificationUrl;
            $_SESSION['verification_email'] = $email;
            
            // Również loguj
            error_log("=== EMAIL WERYFIKACYJNY ===");
            error_log("Do: {$email}");
            error_log("Link weryfikacyjny: {$verificationUrl}");
            error_log("Token: {$token}");
            error_log("===========================");
            
            return true;
        }
        
        $verificationUrl = url("verify-email.php?token=" . urlencode($token));
        
        $subject = "Potwierdź swój adres email - EventApp";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; color: #777; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Witaj w EventApp!</h1>
                </div>
                <div class='content'>
                    <p>Dziękujemy za rejestrację!</p>
                    <p>Aby dokończyć proces rejestracji, kliknij poniższy przycisk aby potwierdzić swój adres email:</p>
                    <p style='text-align: center;'>
                        <a href='{$verificationUrl}' class='button'>Potwierdź adres email</a>
                    </p>
                    <p>Lub skopiuj i wklej poniższy link do przeglądarki:</p>
                    <p style='word-break: break-all; background: white; padding: 10px; border-radius: 5px;'>{$verificationUrl}</p>
                    <p><strong>Link jest ważny przez 24 godziny.</strong></p>
                    <p>Jeśli to nie Ty zarejestrowałeś się w naszym serwisie, zignoruj tę wiadomość.</p>
                </div>
                <div class='footer'>
                    <p>EventApp &copy; 2025 | Wszystkie prawa zastrzeżone</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: EventApp <noreply@eventapp.com>',
            'Reply-To: support@eventapp.com',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($email, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Sprawdza czy email użytkownika jest zweryfikowany
     * 
     * @param int $userId
     * @return bool
     */
    public static function isEmailVerified(int $userId): bool
    {
        $query = "SELECT email_verified_at FROM Users WHERE user_id = ?";
        $result = Database::queryOne($query, [$userId]);
        
        return $result !== null && $result['email_verified_at'] !== null;
    }
    
    /**
     * Usuwa wygasłe tokeny weryfikacyjne
     * 
     * @return int Liczba usuniętych tokenów
     */
    public static function cleanupExpiredTokens(): int
    {
        $query = "DELETE FROM EmailVerifications 
                  WHERE expires_at < NOW() 
                  AND verified_at IS NULL";
        
        Database::execute($query);
        
        return Database::getInstance()->lastInsertId();
    }
}
