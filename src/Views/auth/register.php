<?php
// Registration view
?>
<?php include view('partials/head.php'); ?>
<main class="auth-page">
  <?php 
  ob_start();
  include view('partials/auth/register_form.php');
  $formContent = ob_get_clean();
  include view('partials/auth/layout.php');
  ?>
</main>

<script src="<?= asset('js/auth/slider.js') ?>" defer></script>
<script src="<?= asset('js/auth/username-validation.js') ?>" defer></script>
<script src="<?= asset('js/auth/email-validation.js') ?>" defer></script>
<script src="<?= asset('js/auth/password-validation.js') ?>" defer></script>
<script src="<?= asset('js/auth/toggle-password.js') ?>" defer></script>
<script src="<?= asset('js/auth/empty-form-validation.js') ?>" defer></script>
</body>
</html>

