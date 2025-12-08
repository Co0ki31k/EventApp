<?php

/**
 * Klasa PasswordReset - obsługa jednorazowych linków do resetu hasła
 */
class PasswordReset
{
    /**
     * Generuje token resetu dla użytkownika
     * @param int $userId
     * @param int $minutes
     * @return string|false
     */
    public static function generateToken(int $userId, int $minutes = 5): string|false
    {
        $token = bin2hex(random_bytes(16));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));

        $query = "INSERT INTO PasswordResets (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())";
        $result = Database::execute($query, [$userId, $token, $expiresAt]);

        return $result ? $token : false;
    }

    /**
     * Weryfikuje token resetu
     * @param string $token
     * @return array|null
     */
    public static function verifyToken(string $token): ?array
    {
        $query = "SELECT pr.*, u.user_id, u.email FROM PasswordResets pr JOIN Users u ON pr.user_id = u.user_id WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL LIMIT 1";
        $row = Database::queryOne($query, [$token]);
        return $row === false ? null : $row;
    }

    /**
     * Oznacza token jako użyty
     * @param string $token
     * @return bool
     */
    public static function markAsUsed(string $token): bool
    {
        $query = "UPDATE PasswordResets SET used_at = NOW() WHERE token = ?";
        return Database::execute($query, [$token]) ? true : false;
    }

    /**
     * Wysyła e-mail z linkiem resetu
     * @param string $email
     * @param string $token
     * @return bool
     */
    public static function sendResetEmail(string $email, string $token): bool
    {
        // Pobierz username jeśli istnieje
        $userRow = Database::queryOne("SELECT username FROM Users WHERE email = ? LIMIT 1", [$email]);
        $username = $userRow && isset($userRow['username']) ? htmlspecialchars($userRow['username']) : '';

        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['password_reset_email'] = $email;

        // Stały link do resetu (środowisko lokalne)
        $verifyUrl = 'http://localhost/Projekt/public/reset-password.php?token=' . urlencode($token);

        // Prosta, stała ścieżka do logo: `public/assets/img/logo.png`
        $embeddedLogoPath = null;
        if (defined('ASSETS_PATH')) {
            $p = rtrim(ASSETS_PATH, '/\\') . '/img/logo.png';
            if (file_exists($p)) $embeddedLogoPath = $p;
        }
        if (!$embeddedLogoPath) {
            $p2 = rtrim(BASE_PATH, '/\\') . '/public/assets/img/logo.png';
            if (file_exists($p2)) $embeddedLogoPath = $p2;
        }

        if ($embeddedLogoPath) {
            $logoImgHtml = "<img src=\"cid:logo_cid\" alt=\"EventApp\" style=\"height:40px;display:block;margin:0 auto;\">";
        } else {
            $logoImgHtml = "<div class=\"logo\">EventApp</div>";
        }

        $subject = "Reset hasła — EventApp";

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
                .greeting { font-size:16px; margin:0 0 12px 0; }
                .intro { margin:12px 0; font-size:14px; }
                .btn { display:inline-block; background:#28a745; color:#fff; padding:15px 21px; border-radius:8px; text-decoration:none; font-weight:700; }
                .meta { margin-top:16px; color:#586069; font-size:13px; }
                .note { margin-top:12px; font-size:13px; color:#586069; }
                .footer {padding:12px 24px; background:#f6f8fa; color:#586069; font-size:12px; text-align:center; }
            </style>
        </head>
        <body>
            <div class="wrapper">
                <div class="card">
                    <div class="card-header">
                        {$logoImgHtml}
                    </div>
                    <div class="card-body">
                        <p class="greeting">Witaj, <strong>{$username}</strong></p>
                        <p class="intro">Kliknij przycisk poniżej, aby ustawić nowe hasło.</p>
                        <p style="text-align:center;margin:18px 0;"><a class="btn" href="{$verifyUrl}">Ustaw nowe hasło</a></p>
                        <p class="meta">Jeżeli nie prosiłeś o zmianę hasła, zignoruj tę wiadomość.</p>
                        <p class="note">Link jest jednorazowy i ważny przez 5 minut.</p>
                    </div>
                    <div class="footer">EventApp &copy; 2025 — Dziękujemy</div>
                </div>
            </div>
        </body>
        </html>
        HTML;

        // Wyślij przez PHPMailer z fallbackem jak w EmailVerification
        try {
            $autoload = BASE_PATH . '/vendor/autoload.php';
            if (!file_exists($autoload)) throw new \Exception('Composer autoload not found: ' . $autoload);
            require_once $autoload;

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = defined('SMTP_USER') ? SMTP_USER : 'eventapp4@gmail.com';
            $mail->Password = defined('SMTP_PASS') ? SMTP_PASS : 'sois dnxe dlko yhks';
            $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 465;
            $mail->CharSet = 'UTF-8';

            $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@eventapp.com';
            $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'EventApp';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($email);

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
            error_log('PasswordReset send error: ' . $e->getMessage());
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: EventApp <' . (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@eventapp.com') . '>',
                'X-Mailer: PHP/' . phpversion()
            ];
            return mail($email, $subject, $message, implode("\r\n", $headers));
        }
    }
}
