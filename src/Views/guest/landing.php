<?php
// Guest landing view - hero section
// Sekcja hero zajmuje 100vh. Lewa kolumna 45% (tekst + CTA), prawa 55% (obrazek).
?>

<section class="guest-hero">
	<div class="hero-inner">
		<div class="hero-left">
			<h1>Odkryj lokalne<br><span class="accent-underline">życie,</span> które toczy<br>się tuż obok.</h1>
			<h2>Interaktywna mapa mikrowydarzeń tworzonych przez mieszkańców.<br>Od wspólnych treningów o świcie i ulicznych gier miejskich,<br>po wieczorne koncerty pod gołym niebem.</h2>
			<a class="btn btn-primary btn-map" href="#events"><span class="btn-label">Zobacz mapę wydarzeń</span> <span class="btn-arrow">→</span></a>
		</div>

		<div class="hero-right">
			<img src="<?= htmlspecialchars(asset('img/guest/landing_page_hero_img.png')) ?>" alt="Hero image - lokalne wydarzenia">
		</div>
	</div>
</section>
