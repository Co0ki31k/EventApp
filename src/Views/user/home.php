<?php
// User home/dashboard view — ładuje partiale layoutu użytkownika
// Layout: left sidebar (icons), map container (center), topbar (mode switch), profile edit (modal)
?>

<?php include view('partials/head.php'); ?>
<div class="user-home-layout">
	<?php include view('user/partials/topbar.php'); ?>
	<?php include view('user/partials/sidebar.php'); ?>

	<main class="user-map-area">
		<?php if (isset($joinedEventsMessage) && $joinedEventsMessage): ?>
			<div class="notification-banner success" style="position: fixed; top: 7rem; right: 2rem; z-index: 10000; padding: 1.5rem 2rem; border-radius: 0.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); background: linear-gradient(135deg, #16d10596 0%, #4ba259 100%); color: white; opacity: 0;">
				<?= Security::escape($joinedEventsMessage) ?>
			</div>
			<script>
				(function(){
					var banner = document.querySelector('.notification-banner');
					if(banner) {
						setTimeout(function(){ banner.style.opacity = '1'; banner.style.transition = 'opacity 0.3s'; }, 10);
						setTimeout(function(){
							banner.style.opacity = '0';
							setTimeout(function(){ banner.remove(); }, 300);
						}, 4000);
					}
				})();
			</script>
		<?php endif; ?>
		<?php include view('user/partials/map_container.php'); ?>
	</main>

</div> 

<?php // Frontend scripts for the user area
?>

<script> window.currentUserId = <?= isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null' ?>;</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="crossorigin=""></script>
<script src="<?= asset('js/user/map-icons.js') ?>"></script>
<script src="<?= asset('js/user/map-controller.js') ?>"></script>
<script src="<?= asset('js/user/map-markers.js') ?>"></script>
<script src="<?= asset('js/user/add-event-panel.js') ?>"></script>
<script src="<?= asset('js/user/discover-panel.js') ?>"></script>
<script src="<?= asset('js/user/my-events-panel.js') ?>"></script>
<script src="<?= asset('js/user/friends.js') ?>"></script>
<script src="<?= asset('js/user/topbar.js') ?>"></script>
<script src="<?= asset('js/user/sidebar.js') ?>"></script>
<script src="<?= asset('js/user/notifications.js') ?>"></script>
<script src="<?= asset('js/user/settings.js') ?>"></script>