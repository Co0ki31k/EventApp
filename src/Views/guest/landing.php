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
		<h3 class="stats-title">Przełam rutynę i znajdź wydarzenie, które naładuje Cię pozytywną energią.</h3>
		<div class="stats-list">
			<div class="stats-card">
				<div class="stat-number">25k+</div>
				<div class="stat-label">Aktywnych Użytkowników</div>
			</div>
			<div class="stats-card">
				<div class="stat-number">10k+</div>
				<div class="stat-label">Wydarzeń Miesięcznie</div>
			</div>
			<div class="stats-card">
				<div class="stat-number">500</div>
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

<!-- Roles / two-card section (improved appearance) -->
<section class="roles-section">
	<div class="roles-container">
		<h2 class="roles-title">Dwie role, jedna społeczność.</h2>
		<h3 class="roles-subtitle"><span class="grad">Ty decydujesz, kim dzisiaj jesteś.</span></h3>

		<div class="roles-inner">
			<div class="role-card orange">
				<div class="role-top">
					<div class="role-icon">+</div>
					<div>
						<div class="role-title">Zorganizuj coś własnego</div>
						<div class="role-desc">Masz pomysł na spacer z psami, planszówki czy poranną jogę? Dodaj wydarzenie w 30 sekund i znajdź chętnych w sąsiedztwie.</div>
					</div>
				</div>
				<div class="role-cta">
					<a class="role-btn" href="#">Dodaj wydarzenie <span class="btn-arrow">→</span></a>
				</div>
			</div>

			<div class="role-card blue">
				<div class="role-top">
					<div class="role-icon">🔍</div>
					<div>
						<div class="role-title">Dołącz do ekipy</div>
						<div class="role-desc">Nudzisz się? Sprawdź mapę, zobacz, kto właśnie gra w kosza lub idzie na kawę, i dołącz jednym kliknięciem.</div>
					</div>
				</div>
				<div class="role-cta">
					<a class="role-btn" href="#">Przeglądaj mapę <span class="btn-arrow">→</span></a>
				</div>
			</div>
		</div>
	</div>
</section>
