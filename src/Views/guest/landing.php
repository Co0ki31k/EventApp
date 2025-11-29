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

<!-- Stats section (large) under hero - centered title with vertical card list -->
<section class="stats-section">
	<div class="stats-inner">
		<h2 class="stats-title">Tysiące powodów, by wyjść z domu.</h2>
		<div class="stats-list">
			<div class="stats-card featured">
				<div class="stat-number">25k+</div>
				<div class="stat-label">Aktywnych Użytkowników</div>
			</div>
			<div class="stats-card">
				<div class="stat-number">10k+</div>
				<div class="stat-label">Wydarzeń Miesięcznie</div>
			</div>
			<div class="stats-card">
				<div class="stat-number">500+</div>
				<div class="stat-label">Zaplanowanych eventów w Twojej okolicy</div>
			</div>
			<div class="stats-card">
				<div class="stat-number">10k</div>
				<div class="stat-label">Dzielnic i Osiedli w Aplikacji</div>
			</div>
			<div class="stats-card">
				<div class="stat-number">300+</div>
				<div class="stat-label">Lokalnych Twórców i Organizatorów</div>
			</div>
		</div>
	</div>
</section>
