<section class="auth-panel">
  <h1>Stwórz konto</h1>
  <p class="muted">Dołącz, aby odkrywać i tworzyć lokalne wydarzenia.</p>

  <?php if (!empty($success) && $success): ?>
    <div class="form-success" style="background: #c6f6d5; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
      <strong>✓ Sukces!</strong> <?= Security::escape($message) ?>
      <?php if (defined('APP_ENV') && APP_ENV === 'development' && isset($_SESSION['verification_link'])): ?>
        <div style="margin-top: 1rem; padding: 1rem; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px;">
          <p style="margin: 0 0 0.5rem 0; color: #856404;"><strong>🔧 Tryb deweloperski - Link weryfikacyjny:</strong></p>
          <a href="<?= Security::escape($_SESSION['verification_link']) ?>" 
             style="color: #667eea; word-break: break-all; text-decoration: underline;"
             target="_blank">
            <?= Security::escape($_SESSION['verification_link']) ?>
          </a>
          <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: #856404;">
            <em>W produkcji ten link zostałby wysłany na adres: <?= Security::escape($_SESSION['verification_email'] ?? '') ?></em>
          </p>
        </div>
      <?php elseif (class_exists('EmailVerification')): ?>
        <p style="margin-top: 0.5rem;">Sprawdź swoją skrzynkę email, aby zweryfikować konto.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($message) && !$success): ?>
    <div class="form-errors" style="background: #fed7d7; color: #742a2a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
      <strong>✗ Błąd:</strong> <?= Security::escape($message) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($errors) && is_array($errors)): ?>
    <div class="form-errors">
      <ul>
        <?php foreach ($errors as $field => $error): ?>
          <li><?= Security::escape($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="register.php" method="post" class="form-register" novalidate>
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken ?? '') ?>">

    <div class="form-row">
      <label for="username">Nazwa użytkownika</label>
      <input id="username" name="username" type="text" value="<?= isset($old['username']) ? Security::escape($old['username']) : '' ?>" maxlength="50" required>
      <?php if (isset($errors['username'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 0.875rem;"><?= Security::escape($errors['username']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-row">
      <label for="email">E‑mail</label>
      <input id="email" name="email" type="email" value="<?= isset($old['email']) ? Security::escape($old['email']) : '' ?>" required>
      <?php if (isset($errors['email'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 0.875rem;"><?= Security::escape($errors['email']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-row">
      <label for="password">Hasło</label>
      <div class="password-input-wrapper">
        <input id="password" name="password" type="password" minlength="8" required>
        <button type="button" class="toggle-password" aria-label="Pokaż hasło">
          <img src="<?= asset('img/register-login/show.png') ?>" alt="Pokaż hasło" class="eye-icon">
        </button>
      </div>
      <?php if (isset($errors['password'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 0.875rem; display: block; margin-top: 0.5rem;"><?= Security::escape($errors['password']) ?></span>
      <?php endif; ?>
      <div class="password-checklist">
        <div class="checklist-item password-strength" data-rule="strength">
          <span class="check-icon">X</span>
          <span class="check-text">Siła hasła: <span class="strength-value">słabe</span></span>
        </div>
        <div class="checklist-item" data-rule="length">
          <span class="check-icon">X</span>
          <span class="check-text">Minimum 8 znaków</span>
        </div>
        <div class="checklist-item" data-rule="uppercase">
          <span class="check-icon">X</span>
          <span class="check-text">Jedna wielka litera</span>
        </div>
        <div class="checklist-item" data-rule="lowercase">
          <span class="check-icon">X</span>
          <span class="check-text">Jedna mała litera</span>
        </div>
        <div class="checklist-item" data-rule="number">
          <span class="check-icon">X</span>
          <span class="check-text">Jedna cyfra</span>
        </div>
        <div class="checklist-item" data-rule="specialcase">
          <span class="check-icon">X</span>
          <span class="check-text">Jeden znak specjalny</span>
        </div>
      </div>
    </div>

    <!-- Hidden field for role (domyślnie 'user') -->
    <input type="hidden" name="role" value="user">

    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Zarejestruj się</button>
      <a href="login.php" class="btn btn-link">Masz już konto? Zaloguj się</a>
    </div>
  </form>
</section>
