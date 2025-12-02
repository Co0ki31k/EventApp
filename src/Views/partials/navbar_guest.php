<?php
// Guest navbar partial
?>
<nav class="guest-nav">
  <div class="guest-nav-inner">
    <div class="guest-nav-left">
      <a href="/" class="guest-brand">
        <img src="<?= htmlspecialchars(asset('img/logo.png')) ?>" alt="EventApp" class="guest-nav-logo">
        <span class="guest-brand-text">EventApp</span>
      </a>
    </div>

    <div class="guest-nav-middle">
      <div class="nav-dropdown" id="howDropdown">
        <button class="nav-link dropdown-toggle" aria-controls="howDropdownMenu" id="howToggle" type="button">Jak to działa?</button>
        <ul class="dropdown-menu" id="howDropdownMenu" role="menu" aria-labelledby="howToggle">
          <li role="none"><div role="presentation" class="dropdown-item">Zobacz mapę wydarzeń</div></li>
          <li role="none"><div role="presentation" class="dropdown-item">Dołącz do ludzi w okolicy</div></li>
          <li role="none"><div role="presentation" class="dropdown-item">Zorganizuj własne wydarzenie</div></li>
        </ul>
      </div>
      <a href="#business" class="nav-link">Dla firm</a>
    </div>

    <div class="guest-nav-right">
      <a href="/login" class="nav-link nav-login">Logowanie</a>
      <a href="/register" class="nav-link nav-register">Rejestracja</a>
    </div>
  </div>
</nav>
