<?php
// Login view
?>
<?php include view('partials/head.php'); ?>
<main class="auth-page">
  <?php 
  ob_start();
  include view('partials/auth/login_form.php');
  $formContent = ob_get_clean();
  include view('partials/auth/layout.php');
  ?>
</main>

<script src="<?= asset('js/auth/slider.js') ?>" defer></script>
</body>
</html>
