<?php
// User home/dashboard view — ładuje partiale layoutu użytkownika
// Layout: left sidebar (icons), map container (center), topbar (mode switch), profile edit (modal)
?>

<?php include view('partials/head.php'); ?>
<div class="user-home-layout">
	<?php include view('user/partials/topbar.php'); ?>
	<?php include view('user/partials/sidebar.php'); ?>

	<main class="user-map-area">
		<?php include view('user/partials/map_container.php'); ?>
	</main>

</div> 

<?php // Frontend scripts for the user area
?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="crossorigin=""></script>
<script src="<?= asset('js/user/map-controller.js') ?>"></script>
<script src="<?= asset('js/user/add-event-panel.js') ?>"></script>
<script src="<?= asset('js/user/topbar.js') ?>"></script>
<script src="<?= asset('js/user/sidebar.js') ?>"></script>