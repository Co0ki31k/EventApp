<?php
// Admin home view — strona główna panelu administratora
?>

<?php include view('partials/head.php'); ?>
<div class="admin-home-layout">
	<?php include view('admin/partials/sidebar.php'); ?>
    <?php include view('admin/partials/topbar.php'); ?>
	
	<div class="admin-main-wrapper">
		<?php include view('admin/partials/content.php'); ?>
	</div>
</div>

<script src="<?= asset('js/admin/admin-sidebar.js') ?>"></script>
<script src="<?= asset('js/admin/admin-topbar.js') ?>"></script>
<script src="<?= asset('js/admin/dashboard.js') ?>"></script>
<script src="<?= asset('js/admin/users.js') ?>"></script>
<script src="<?= asset('js/admin/events.js') ?>"></script>
