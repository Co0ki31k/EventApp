<?php
// Guest home/map view — strona z mapą wydarzeń dla gości
// Layout: navbar dla gości, mapa, call-to-action, footer
?>

<?php include view('partials/head.php'); ?>
<div class="guest-home-layout">
	<?php include view('guest/partials/topbar.php'); ?>
	<main class="guest-map-area">
		<?php include view('guest/partials/map_container.php'); ?>
	</main>
</div>
<?php // Frontend scripts for the guest area ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="crossorigin=""></script>
<script src="<?= asset('js/guest/map-controller.js') ?>"></script>
<script src="<?= asset('js/guest/topbar.js') ?>"></script>
<script src="<?= asset('js/guest/pending-events-dropdown.js') ?>"></script>