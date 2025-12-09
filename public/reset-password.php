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

$errors = [];
$message = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $errors[] = 'Brak tokenu resetu.';
} else {
    $reset = PasswordReset::verifyToken($token);
    if ($reset === null) {
        $errors[] = 'Link resetu jest nieprawidłowy lub wygasł.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $errors[] = 'Hasło musi mieć co najmniej 8 znaków.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Hasła nie są takie same.';
    }

    if (empty($errors)) {
        // ponownie pobierz reset
        $reset = PasswordReset::verifyToken($token);
        if ($reset === null) {
            $errors[] = 'Token jest nieprawidłowy lub wygasł.';
        } else {
            // Zapisz nowe hasło przez model User
            User::changePassword((int)$reset['user_id'], $password);
            PasswordReset::markAsUsed($token);
            // opcjonalnie: wyczyść sesje użytkownika (nieimplementowane)
            header('Location: login.php?reset=1');
            exit;
        }
    }
}

include view('partials/head.php');
$html_class = 'auth-root';
?>
<main class="auth-page">
<?php
    ob_start();
?>
<section class="auth-panel">
  <h1>Ustaw nowe hasło</h1>
  <p class="muted">Wprowadź nowe hasło dla konta powiązanego z tym linkiem.</p>

  <?php if (!empty($message)): ?>
    <div class="form-success" style="background: #c6f6d5; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
      <?= Security::escape($message) ?></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="form-errors" style="color: #e53e3e; font-size: 1.2rem; display: block; margin-top: 0.5rem;">
      <?= Security::escape(implode('<br>', $errors)) ?></div>
  <?php endif; ?>

  <?php if (empty($errors) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
  <form method="post" class="form-login">
    <div class="form-row">
      <label for="password">Nowe hasło</label>
      <div class="password-input-wrapper">
        <input id="password" name="password" type="password" required minlength="8" autofocus>
        <button type="button" class="toggle-password" aria-label="Pokaż hasło">
          <img src="<?= asset('img/register-login/show.png') ?>" alt="Pokaż hasło" class="eye-icon">
        </button>
      </div>
    </div>
    <div class="form-row">
      <label for="password_confirm">Powtórz hasło</label>
      <div class="password-input-wrapper">
        <input id="password_confirm" name="password_confirm" type="password" required minlength="8">
        <button type="button" class="toggle-password" aria-label="Pokaż hasło">
          <img src="<?= asset('img/register-login/show.png') ?>" alt="Pokaż hasło" class="eye-icon">
        </button>
      </div>
    </div>
    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Ustaw hasło</button>
      <a href="login.php" class="btn btn-link">Wróć do logowania</a>
    </div>
  </form>
  <?php endif; ?>
</section>
<?php
    $formContent = ob_get_clean();
    include view('partials/auth/layout.php');
?>
</main>
<script src="<?= asset('js/auth/slider.js') ?>" defer></script>
<script src="<?= asset('js/auth/toggle-password.js') ?>" defer></script>
</body>
</html>
