<?php
require_once "../config/paths.php";
require_once "../includes/header.php";
require_once "../includes/navbar.php";
?>


<!-- Fixed header w navbar.php -->

<!-- Hero + statystyki w jednym prostokącie -->
<section class="hero-stats">
    <div class="hero-stats-container">
        <!-- Tekst główny -->
        <div class="hero-text">
            <h1>Odkrywaj najciekawsze wydarzenia w Twoim mieście!</h1>
            <p>Znajdź wydarzenia w okolicy, dołącz do społeczności i nie przegap żadnej okazji.</p>
            <a href="explore.php" class="btn-primary">Otwórz mapę</a>
        </div>

        <!-- Statystyki -->
        <div class="stats-container">
            <div class="stat-item">
                <h2>120+</h2>
                <p>Wydarzeń</p>
            </div>
            <div class="stat-item">
                <h2>5000+</h2>
                <p>Użytkowników</p>
            </div>
            <div class="stat-item">
                <h2>50+</h2>
                <p>Miast</p>
            </div>
        </div>
    </div>
</section>

<!-- Co to jest za rozwiązanie -->
<section class="about-solution">
    <div class="about-container">
        <h2>Co to jest MojaStrona?</h2>
        <p>Nasza platforma pozwala w prosty sposób odkrywać wydarzenia, łączyć się ze znajomymi i zarządzać własnymi planami aktywności.</p>
    </div>
</section>

<!-- Jak działa -->
<section class="how-it-works">
    <div class="how-container">
        <h2>Jak to działa?</h2>
        <ul>
            <li>Przeglądaj mapę i listę wydarzeń</li>
            <li>Wybieraj interesujące Cię wydarzenia</li>
            <li>Dołączaj do społeczności i śledź aktywności</li>
        </ul>
    </div>
</section>

<!-- Część dla firm -->
<section class="for-companies">
    <div class="company-container">
        <h2>Dla firm</h2>
        <p>Zarejestruj swoją firmę, dodawaj wydarzenia i promuj je wśród użytkowników naszej platformy.</p>

        <!-- Slider logotypów firm -->
        <div class="company-slider">
            <img src="logos/logo1.png" alt="Firma 1">
            <img src="logos/logo2.png" alt="Firma 2">
            <img src="logos/logo3.png" alt="Firma 3">
            <img src="logos/logo4.png" alt="Firma 4">
        </div>
    </div>
</section>

<!-- Stopka -->
<footer class="footer">
    <div class="footer-container">
        <p>&copy; 2025 MojaStrona. Wszelkie prawa zastrzeżone.</p>
    </div>
</footer>

</body>
</html>