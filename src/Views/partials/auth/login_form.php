<section class="auth-panel">
  <h1>Zaloguj się</h1>
  <p class="muted">Witaj ponownie! Wprowadź swoje dane, aby się zalogować.</p>

  <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
    <div class="form-success" style="background: #c6f6d5; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
      <strong>✓ Rejestracja zakończona!</strong> Twoje konto zostało aktywowane. Możesz się teraz zalogować.
    </div>
  <?php endif; ?>

  <?php if (isset($success)): ?>
    <div class="form-success">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <form action="login.php" method="post" class="form-login">
    <div class="form-row">
      <label for="email">E‑mail</label>
      <input id="email" name="email" type="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" required autofocus>
      <?php if (isset($errors['email'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 1.2rem; display: block; margin-top: 0.5rem;"><?= htmlspecialchars($errors['email']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-row">
      <label for="password">Hasło</label>
      <div class="password-input-wrapper">
        <input id="password" name="password" type="password" required minlength="8">
        <button type="button" class="toggle-password" aria-label="Pokaż hasło">
          <img src="<?= asset('img/register-login/show.png') ?>" alt="Pokaż hasło" class="eye-icon">
        </button>
      </div>
      <?php if (isset($errors['password'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 1.2rem; display: block; margin-top: 0.5rem;"><?= htmlspecialchars($errors['password']) ?></span>
      <?php endif; ?>
      <?php if (isset($errors['general'])): ?>
        <span class="error-text" style="color: #e53e3e; font-size: 1.2rem; display: block; margin-top: 0.5rem; font-weight: 600;"><?= htmlspecialchars($errors['general']) ?></span>
      <?php endif; ?>
    </div>

    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Zaloguj się</button>
      <a href="register.php" class="btn btn-link">Nie masz konta? Zarejestruj się</a>
    </div>
  </form>
</section>
