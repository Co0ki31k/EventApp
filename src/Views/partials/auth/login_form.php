<section class="auth-panel">
  <h1>Zaloguj się</h1>
  <p class="muted">Witaj ponownie! Wprowadź swoje dane, aby się zalogować.</p>

  <?php if (!empty($errors) && is_array($errors)): ?>
    <div class="form-errors">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (isset($success)): ?>
    <div class="form-success">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <form action="/login" method="post" class="form-login">
    <?php /* echo csrf_field(); */ ?>

    <div class="form-row">
      <label for="email">E‑mail</label>
      <input id="email" name="email" type="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" required autofocus>
    </div>

    <div class="form-row">
      <label for="password">Hasło</label>
      <input id="password" name="password" type="password" required minlength="8">
    </div>

    <div class="form-row">
      <label class="checkbox-inline">
        <input type="checkbox" name="remember" value="1" <?= !empty($old['remember']) ? 'checked' : '' ?>>
        Zapamiętaj mnie
      </label>
    </div>

    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Zaloguj się</button>
      <a href="/forgot-password" class="btn btn-link">Zapomniałeś hasła?</a>
    </div>
  </form>

  <div class="auth-footer">
    <p>Nie masz jeszcze konta? <a href="/register">Zarejestruj się</a></p>
  </div>
</section>
