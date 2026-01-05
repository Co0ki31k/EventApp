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
        $token = bin2hex(random_bytes(16));
        $code = random_int(100000, 999999); // 6-cyfrowy kod
        // Kod ważny 5 minut
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $query = "INSERT INTO EmailVerifications (user_id, token, code, expires_at, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";

        $result = Database::execute($query, [$userId, $token, (string)$code, $expiresAt]);

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
                       SET email_verified = 1 
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
     * Weryfikuje kod numeryczny (6 cyfr) dla użytkownika
     *
     * @param int $userId
     * @param string $code
     * @return bool
     */
    public static function verifyCodeForUser(int $userId, string $code): bool
    {
        $query = "SELECT * FROM EmailVerifications 
                  WHERE user_id = ? AND code = ? AND expires_at > NOW() AND verified_at IS NULL LIMIT 1";

        $verification = Database::queryOne($query, [$userId, $code]);

        if ($verification === false || $verification === null) {
            return false;
        }

        return self::markAsVerified($verification['token']);
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
        // Pobierz kod z bazy
        $row = Database::queryOne("SELECT code FROM EmailVerifications WHERE token = ? LIMIT 1", [$token]);
        $code = $row && isset($row['code']) ? $row['code'] : '';

        // Zapisz w sesji i loguj także w trybie development (ułatwienie testów)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['verification_email'] = $email;
        $_SESSION['verification_code'] = $code;

        // Pobierz nazwę użytkownika (jeśli dostępna)
        $userRow = Database::queryOne("SELECT username FROM Users WHERE email = ? LIMIT 1", [$email]);
        $username = $userRow && isset($userRow['username']) ? htmlspecialchars($userRow['username']) : '';

        // Prosta, stała ścieżka do logo: `public/assets/img/logo.png` (użyj ASSETS_PATH jeśli zdefiniowane)
        $embeddedLogoPath = null;
        if (defined('ASSETS_PATH')) {
            $p = rtrim(ASSETS_PATH, '/\\') . '/img/logo.png';
            if (file_exists($p)) $embeddedLogoPath = $p;
        }
        if (!$embeddedLogoPath) {
            $p2 = rtrim(BASE_PATH, '/\\') . '/public/assets/img/logo.png';
            if (file_exists($p2)) $embeddedLogoPath = $p2;
        }

        // przygotuj HTML nagłówka z logo — jeśli lokalny plik istnieje, użyj CID, inaczej tekst
        if ($embeddedLogoPath) {
            $logoImgHtml = "<img src=\"cid:logo_cid\" alt=\"EventApp\" style=\"height:40px;display:block;margin:0 auto;\">";
        } else {
            $logoImgHtml = "<div class=\"logo\">EventApp</div>";
        }

        // Przygotuj treść maila (polski, prosty czytelny szablon)
        $subject = "Kod weryfikacyjny — EventApp";

        $message = <<<HTML
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#24292e; }
                .wrapper { max-width:600px; margin:20px auto; }
                .card { border:1px solid #e1e4e8; border-radius:6px; overflow:hidden; }
                .card-header { background:#fafbfc; padding:18px 24px; text-align:center; }
                .logo { font-weight:700; color:#24292e; font-size:20px; }
                .card-body { background:#fff; padding:24px; }
                .greeting { font-size:16px; margin:0 0 10px 0; }
                .intro { margin:12px 0; font-size:14px; }
                .code-box { text-align:center; margin:18px 0; }
                .code { display:inline-block; background:#f6f8fa; border:1px solid #e1e4e8; padding:18px 28px; font-size:28px; letter-spacing:6px; border-radius:6px; font-weight:700; }
                .meta { margin-top:12px; color:#586069; font-size:13px; }
                .note { margin-top:18px; font-size:13px; color:#586069; }
                .footer { margin-top:20px; padding:12px 24px; background:#f6f8fa; color:#586069; font-size:12px; text-align:center; }
            </style>
        </head>
        <body>
            <div class="wrapper">
                <div class="card">
                    <div class="card-header">
                        {$logoImgHtml}
                    </div>
                    <div class="card-body">
                        <p class="greeting">Potwierdź swoją tożsamość, <strong>{$username}</strong></p>
                        <p class="intro">Oto Twój jednorazowy kod weryfikacyjny:</p>
                        <div class="code-box"><div class="code">{$code}</div></div>
                        <p class="meta"><strong>Kod jest ważny przez 5 minut i może zostać użyty tylko raz.</strong></p>
                        <p class="note">Prosimy, nie udostępniaj tego kodu nikomu. Jeśli to nie Ty prosiłeś o kod, zignoruj tę wiadomość.</p>
                    </div>
                    <div class="footer">EventApp &copy; 2025 — Dziękujemy</div>
                </div>
            </div>
        </body>
        </html>
        HTML;

        // Spróbuj wysłać mail przez PHPMailer (wymaga zainstalowanego composera i ustawień SMTP)
        try {
            $autoload = BASE_PATH . '/vendor/autoload.php';
            if (!file_exists($autoload)) {
                throw new \Exception('Composer autoload not found: ' . $autoload);
            }
            require_once $autoload;

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            // Ustawienia SMTP (możesz zdefiniować stałe w config/app_config.php: SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE, SMTP_FROM_EMAIL, SMTP_FROM_NAME)
            $mail->isSMTP();
            $mail->Host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = defined('SMTP_USER') ? SMTP_USER : 'eventapp4@gmail.com';
            $mail->Password = defined('SMTP_PASS') ? SMTP_PASS : 'sutu ifao dvht gich';
            $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 465;
            $mail->CharSet = 'UTF-8';

            $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'eventapp4@gmail.com';
            $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'EventApp';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($email);
            // Jeśli plik logo istnieje lokalnie, osadź go jako CID
            if ($embeddedLogoPath && file_exists($embeddedLogoPath)) {
                try {
                    $mail->addEmbeddedImage($embeddedLogoPath, 'logo_cid');
                } catch (\Exception $ex) {
                    error_log('Embed logo failed: ' . $ex->getMessage());
                }
            }
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('PHPMailer send error: ' . $e->getMessage());
            // Fallback: spróbuj użyć funkcji mail()
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: EventApp <' . (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@eventapp.com') . '>',
                'Reply-To: support@eventapp.com',
                'X-Mailer: PHP/' . phpversion()
            ];
            return mail($email, $subject, $message, implode("\r\n", $headers));
        }
    }

    /**
     * Sprawdza czy email użytkownika jest zweryfikowany
     * 
     * @param int $userId
     * @return bool
     */
    public static function isEmailVerified(int $userId): bool
    {
        $query = "SELECT email_verified FROM Users WHERE user_id = ?";
        $result = Database::queryOne($query, [$userId]);

        return $result !== null && !empty($result['email_verified']);
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
