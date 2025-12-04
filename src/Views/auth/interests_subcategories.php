<?php
/**
 * Widok wyboru podkategorii zainteresowań - pojedyncza kategoria
 */

// Pobierz ewentualne błędy z sesji
$error = $_SESSION['interests_error'] ?? null;
unset($_SESSION['interests_error']);
?>

<section class="auth-panel">
  <h1>Wybierz swoje zainteresowania</h1>
  <p class="muted">Kategoria: <?= Security::escape($categoryName) ?> (<?= $currentStep ?> z <?= $totalCategories ?>)</p>

  <?php if ($error): ?>
    <div class="form-errors" style="background: #fed7d7; color: #742a2a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
      <strong>✗ Błąd:</strong> <?= Security::escape($error) ?>
    </div>
  <?php endif; ?>

  <form action="interests.php?step=subcategories" method="post" class="form-interests">
    <div class="subcategories-grid">
      <?php foreach ($subcategories as $subcategory): ?>
        <label class="subcategory-item">
          <input 
            type="checkbox" 
            name="subcategories[]" 
            value="<?= $subcategory['subcategory_id'] ?>"
            onchange="this.parentElement.classList.toggle('selected', this.checked); updateCounter()"
          >
          <strong>
            <?= Security::escape($subcategory['name']) ?>
          </strong>
          <?php if (!empty($subcategory['description'])): ?>
            <span>
              <?= Security::escape($subcategory['description']) ?>
            </span>
          <?php endif; ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="form-row form-actions">
      <button type="submit" class="btn btn-primary">
        <?= $currentStep < $totalCategories ? 'Idziemy dalej!' : 'Zakończ' ?>
      </button>
    </div>

    <p style="text-align: center; margin-top: 1.5rem; color: #718096; font-size: 0.875rem;">
      Wybrano: <strong id="selected-count">0</strong> z tej kategorii
    </p>
  </form>
</section>

<script>
// Licznik wybranych zainteresowań
function updateCounter() {
  const checkboxes = document.querySelectorAll('input[name="subcategories[]"]');
  const counter = document.getElementById('selected-count');
  const count = Array.from(checkboxes).filter(cb => cb.checked).length;
  counter.textContent = count;
  counter.style.color = count > 0 ? '#38a169' : '#718096';
}

document.addEventListener('DOMContentLoaded', function() {
  // Animacje przy załadowaniu
  const items = document.querySelectorAll('.subcategory-item');
  items.forEach((item, index) => {
    setTimeout(() => {
      item.style.opacity = '0';
      item.style.transform = 'translateY(10px)';
      item.offsetHeight;
      item.style.transition = 'all 0.3s ease';
      item.style.opacity = '1';
      item.style.transform = 'translateY(0)';
    }, index * 50);
  });
});
</script>
