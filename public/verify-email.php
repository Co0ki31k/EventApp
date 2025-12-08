<?php
require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . '/app_config.php';
require_once SRC_PATH . '/Helpers/url.php';

// Załaduj klasy
load_class('Database');
load_class('Security');
require_once SRC_PATH . '/classes/EmailVerification.php';
require_once SRC_PATH . '/Models/User.php';

session_name(defined('SESSION_NAME') ? SESSION_NAME : 'eventapp_session');
if (session_status() === PHP_SESSION_NONE) session_start();

$message = '';
$errors = [];

// Upewnij się, że mamy tymczasowe ID użytkownika
if (!isset($_SESSION['temp_user_id'])) {
  header('Location: login.php');
  exit;
}

$userId = (int)$_SESSION['temp_user_id'];

// Obsługa POST - weryfikacja kodu lub ponowne wysłanie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['resend'])) {
    // Ponownie wygeneruj token i wyślij
    $token = EmailVerification::generateToken($userId);
    if ($token) {
      // pobierz email użytkownika
      $user = User::findById($userId);
      if ($user) {
        EmailVerification::sendVerificationEmail($user['email'], $token);
        $message = 'Kod został ponownie wysłany na Twój adres e-mail.';
      } else {
        $errors[] = 'Nie znaleziono użytkownika.';
      }
    } else {
      $errors[] = 'Nie można wygenerować kodu. Spróbuj później.';
    }
  } elseif (isset($_POST['code'])) {
    $code = trim($_POST['code']);
    if (empty($code)) {
      $errors[] = 'Podaj kod weryfikacyjny.';
    } else {
      $ok = EmailVerification::verifyCodeForUser($userId, $code);
      if ($ok) {
        $message = 'Email potwierdzony. Przechodzę dalej...';
        // Przejdź do wyboru zainteresowań
        header('Location: interests.php');
        exit;
      } else {
        $errors[] = 'Nieprawidłowy kod lub kod wygasł.';
      }
    }
  }
}

// Render przez layout (slider po lewej, formularz po prawej)
?>
<?php include view('partials/head.php'); ?>
<?php $html_class = 'auth-root'; ?>
<main class="auth-page">
<?php
    ob_start();
    include view('partials/auth/verify_form.php');
    $formContent = ob_get_clean();
    include view('partials/auth/layout.php');
?>
</main>
    <script src="<?= asset('js/auth/slider.js') ?>" defer></script>
    <script src="<?= asset('js/auth/verify.js') ?>" defer></script>
</body>
</html>
