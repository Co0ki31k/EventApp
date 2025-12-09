<?php
// User home/dashboard view — ładuje partiale layoutu użytkownika
// Layout: left sidebar (icons), map container (center), topbar (mode switch), profile edit (modal)
?>

<?php include view('partials/head.php'); ?>
<div class="user-home-layout">
    <?php view('user/partials/topbar'); ?>
	<?php view('user/partials/sidebar'); ?>

	<main class="user-map-area">
		<?php view('user/partials/map_container'); ?>
	</main>

</div>

<?php // Frontend scripts for the user area
?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-nPLbZ6YQmYVhG8h2r0nV3jV5y3s5k2qW1a1q3Y9iV6Q=" crossorigin=""></script>
<script src="<?= asset('js/user/map-controller.js') ?>"></script>
<script src="<?= asset('js/user/sidebar.js') ?>"></script>
<script src="<?= asset('js/user/topbar.js') ?>"></script>
