<section class="auth-panel">
  <h1>Stwórz konto</h1>
  <p class="muted">Dołącz, aby odkrywać i tworzyć lokalne wydarzenia.</p>
  
  <form action="register.php" method="post" class="form-register" novalidate>
    <div class="form-row">
      <label for="username">Nazwa użytkownika</label>
      <input id="username" name="username" type="text" value="<?= isset($old['username']) ? Security::escape($old['username']) : '' ?>" maxlength="50" required>
      <?php if (isset($errors['username'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 1.2rem;"><?= Security::escape($errors['username']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-row">
      <label for="email">E‑mail</label>
      <input id="email" name="email" type="email" value="<?= isset($old['email']) ? Security::escape($old['email']) : '' ?>" required>
      <?php if (isset($errors['email'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 1.2rem;"><?= Security::escape($errors['email']) ?></span>
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
        <span class="error-text" style="color: #e53e3e; font-size: 1.2rem; display: block; margin-top: 0.5rem;"><?= Security::escape($errors['password']) ?></span>
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
