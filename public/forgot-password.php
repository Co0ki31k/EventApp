<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';

load_class('Database');
load_class('Security');
require_once SRC_PATH . '/classes/PasswordReset.php';
require_once SRC_PATH . '/Models/User.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Podaj poprawny adres e‑mail.';
    } else {
        $user = User::findByEmail($email);
        
        if (!$user) {
            $message = 'Jeśli konto o podanym adresie istnieje, wysłaliśmy link do resetu hasła.';
        } else {
            $token = PasswordReset::generateToken((int)$user['user_id']);
            if ($token) {
                PasswordReset::sendResetEmail($email, $token);
                $message = 'Jeśli konto o podanym adresie istnieje, wysłaliśmy link do resetu hasła.';
            } else {
                $errors[] = 'Wystąpił błąd przy generowaniu linku. Spróbuj ponownie.';
            }
        }
    }
}

// Renderuj prosty formularz w ramach layout auth
include view('partials/head.php');
$html_class = 'auth-root';
?>
<main class="auth-page">
<?php
    ob_start();
?>
<section class="auth-panel">
  <h1>Reset hasła</h1>
  <p class="muted">Podaj adres e-mail powiązany z kontem — wyślemy jednorazowy link.</p>

  <?php if (!empty($message)): ?>
    <div class="form-success" style="background: #c6f6d5; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <?= Security::escape($message) ?></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="form-errors" style="color: #e53e3e; font-size: 1.2rem; display: block; margin-top: 0.5rem;">
        <?= Security::escape(implode('<br>', $errors)) ?></div>
  <?php endif; ?>

  <form method="post" class="form-login">
    <div class="form-row">
      <label for="email">E‑mail</label>
      <input id="email" name="email" type="email" required autofocus>
    </div>
    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Wyślij link</button>
      <a href="login.php" class="btn btn-link">Wróć do logowania</a>
    </div>
  </form>
</section>
<?php
    $formContent = ob_get_clean();
    include view('partials/auth/layout.php');
?>
</main>
<script src="<?= asset('js/auth/slider.js') ?>" defer></script>
</body>
</html>
