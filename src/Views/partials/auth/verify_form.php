<section class="auth-panel verify-panel">
  <?php
    // Pobierz email i dane weryfikacji (jeśli dostępne)
    $email = '';
    $maskedEmail = '';
    $expiresIn = null;
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['verification_email'])) {
      $email = $_SESSION['verification_email'];
    } else {
      // fallback: spróbuj pobrać po temp_user_id
      if (!empty($_SESSION['temp_user_id'])) {
        $u = User::findById((int)$_SESSION['temp_user_id']);
        if ($u && !empty($u['email'])) {
          $email = $u['email'];
        }
      }
    }

    // pobierz najnowszy niewykorzystany wpis w EmailVerifications dla current user
    $expiresAt = null;
    if (!empty($_SESSION['temp_user_id'])) {
      $ver = Database::queryOne("SELECT expires_at FROM EmailVerifications WHERE user_id = ? AND verified_at IS NULL ORDER BY created_at DESC LIMIT 1", [ (int)$_SESSION['temp_user_id'] ]);
      if ($ver && !empty($ver['expires_at'])) {
        $expiresAt = $ver['expires_at'];
        $diff = strtotime($expiresAt) - time();
        $expiresIn = $diff > 0 ? $diff : 0;
      }
    }
  ?>

  <div class="verify-card">
    <div class="verify-header">
      <div class="verify-icon">✉️</div>
      <h2>Potwierdz swojego maila</h2>
    </div>
    <div class="verify-body">
      <p class="verify-intro">Kod weryfikujacy został wysłany do <strong><?= Security::escape($email) ?></strong></p>
      <?php if ($expiresIn !== null): ?>
        <p class="verify-expiry">Kod wygaśnie za <span id="verify-countdown"><?= intval($expiresIn) ?></span> sekund.</p>
      <?php endif; ?>

      <?php if (!empty($message)): ?>
        <div class="notice success"><?= Security::escape($message) ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="notice error"><?= Security::escape(implode('<br>', $errors)) ?></div>
      <?php endif; ?>

      <form method="post" class="verify-form" id="verifyForm" novalidate>
        <input type="hidden" name="code" id="codeInput">
        <div class="code-inputs" aria-label="Verification code">
          <?php for ($i=0;$i<6;$i++): ?>
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="digit" data-index="<?= $i ?>" aria-label="Digit <?= $i+1 ?>">
          <?php endfor; ?>
        </div>
        <button type="submit" class="btn-verify">Weryfikuj</button>
      </form>

      <div class="verify-links">
        <form method="post" style="display:inline;" id="resendForm">
          <input type="hidden" name="resend" value="1">
          <button type="submit" class="link-like">Wyślij ponownie</button>
        </form>
        <a href="register.php" class="link-like">Zmień email</a>
      </div>
    </div>
  </div>

  
  <!-- <script src="<?= asset('js/auth/verify.js') ?>" defer></script> -->

</section>
