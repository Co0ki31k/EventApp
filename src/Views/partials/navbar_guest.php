<?php
// Navbar dla gościa (partial)
?>
<header class="site-header">
  <nav class="navbar">
    <div class="container navbar-inner">
      <a class="brand" href="<?= htmlspecialchars(url()) ?>">
        <?= htmlspecialchars(defined('APP_NAME') ? APP_NAME : 'EventApp') ?>
      </a>
      <ul class="nav-links">
        <li><a href="#about">O nas</a></li>
        <li><a href="#events">Wydarzenia</a></li>
        <li><a href="<?= htmlspecialchars(url('login.php')) ?>" class="btn btn-outline">Zaloguj</a></li>
        <li><a href="<?= htmlspecialchars(url('register.php')) ?>" class="btn btn-primary">Zarejestruj</a></li>
      </ul>
    </div>
  </nav>
</header>
