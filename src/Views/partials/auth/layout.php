<!-- Auth layout wrapper: slider + form -->
<div class="split-auth">
    <div class="auth-left">
        <?php include view('partials/auth/slider.php'); ?>
    </div>
    <div class="auth-right">
        <?php if (isset($formContent)): ?>
            <?= $formContent ?>
        <?php endif; ?>
    </div>
</div>
