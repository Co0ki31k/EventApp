<?php
/**
 * Widok wyboru kategorii zainteresowań
 */

// Pobierz ewentualne błędy z sesji
$error = $_SESSION['interests_error'] ?? null;
unset($_SESSION['interests_error']);
?>

<section class="auth-panel">
  <h1>Wybierz swoje zainteresowania</h1>
  <p class="muted">Pomoże nam to polecać Ci wydarzenia dopasowane do Twoich zainteresowań.</p>

  <?php if ($error): ?>
    <div class="form-errors" style="background: #fed7d7; color: #742a2a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
      <strong>✗ Błąd:</strong> <?= Security::escape($error) ?>
    </div>
  <?php endif; ?>

  <form action="interests.php" method="post" class="form-interests">
    <div class="categories-grid">
      <?php foreach ($categories as $category): ?>
        <label class="category-card">
          <input 
            type="checkbox" 
            name="categories[]" 
            value="<?= $category['category_id'] ?>"
            onchange="this.parentElement.classList.toggle('selected', this.checked)"
          >
          <strong>
            <?= Security::escape($category['name']) ?>
          </strong>
          <span>
            <?= Security::escape($category['description']) ?>
          </span>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">Idziemy dalej!</button>
    </div>
  </form>
</section>

<script>
// Dodaj animacje przy załadowaniu
document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('.category-card');
  cards.forEach((card, index) => {
    setTimeout(() => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(10px)';
      card.offsetHeight;
      card.style.transition = 'all 0.3s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 50);
  });
});
</script>
