<section class="auth-panel">
  <h1>Zarejestruj się</h1>
  <p class="muted">Stwórz konto, aby odkrywać i tworzyć lokalne wydarzenia.</p>

  <?php if (!empty($errors) && is_array($errors)): ?>
    <div class="form-errors">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="/register" method="post" enctype="multipart/form-data" class="form-register">
    <?php /* echo csrf_field(); */ ?>

    <div class="form-row">
      <label for="name">Imię</label>
      <input id="name" name="name" type="text" value="<?= isset($old['name']) ? htmlspecialchars($old['name']) : '' ?>" required maxlength="100">
    </div>

    <div class="form-row">
      <label for="email">E‑mail</label>
      <input id="email" name="email" type="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" required>
    </div>

    <div class="form-row two-col">
      <div>
        <label for="password">Hasło</label>
        <input id="password" name="password" type="password" required minlength="8">
      </div>
      <div>
        <label for="password_confirm">Powtórz hasło</label>
        <input id="password_confirm" name="password_confirm" type="password" required minlength="8">
      </div>
    </div>

    <fieldset class="form-row">
      <legend>Zainteresowania (wybierz):</legend>
      <?php
        $interests = ['Sport','Muzyka','Spotkania','Sztuka','Technologie'];
        $selected = isset($old['interests']) && is_array($old['interests']) ? $old['interests'] : [];
      ?>
      <div class="interests-grid">
        <?php foreach ($interests as $i): $id = 'intr_'.preg_replace('/[^a-z0-9]+/i','_',strtolower($i)); ?>
          <label class="checkbox-inline" for="<?= $id ?>">
            <input type="checkbox" id="<?= $id ?>" name="interests[]" value="<?= htmlspecialchars($i) ?>" <?= in_array($i,$selected) ? 'checked' : '' ?>>
            <?= htmlspecialchars($i) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <div class="form-row">
      <label for="avatar">Avatar (opcjonalnie)</label>
      <input id="avatar" name="avatar" type="file" accept="image/*">
      <small class="muted">jpg, png — maks. 2 MB</small>
    </div>

    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Zarejestruj się</button>
      <a href="/login" class="btn btn-link">Masz już konto? Zaloguj się</a>
    </div>
  </form>
</section>
