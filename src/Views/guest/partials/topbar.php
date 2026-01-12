<?php
// Guest topbar - przezroczysta nawigacja z przyciskami po prawej stronie
require_once SRC_PATH . '/Helpers/pending_events.php';
$pendingCount = getPendingEventsCount();
?>

<div class="guest-topbar">
	<div class="topbar-left">
		<button id="yourEventsToggle" class="topbar-btn topbar-your-events" aria-expanded="false" aria-controls="yourEventsMenu">
			Twoje zapisane wydarzenia
			<?php if ($pendingCount > 0): ?>
				<span class="badge-count"><?= $pendingCount ?></span>
			<?php endif; ?>
		</button>
		<div id="yourEventsMenu" class="topbar-dropdown" role="menu" aria-hidden="true">
			<div class="dropdown-content">
				<div id="pendingEventsPlaceholder">
					<!-- Lista zapisanych wydarzeń zostanie załadowana dynamicznie przez JS -->
				</div>
			</div>
		</div>
	</div>

	<div class="topbar-actions">
		<a href="<?= url('login.php') ?>" class="nav-link nav-login">Logowanie</a>
		<a href="<?= url('register.php?from=guest') ?>" class="nav-link nav-register">Rejestracja</a>
	</div>
</div>