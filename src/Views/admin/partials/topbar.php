<?php 
use Random\Engine\Secure;
?><!-- Admin Topbar -->
<header class="admin-topbar">
    <div class="admin-topbar__right">
        <div class="admin-topbar__profile" id="adminProfileBtn">
            <i class="fas fa-user-circle" style="font-size: 2.5rem; color: #7f8c8d;"></i>
            <div class="admin-topbar__profile-info">
                <span><?= Security::escape($_SESSION['username']) ?></span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 0.875rem; margin-left: 0.5rem;"></i>
        </div>
        
        <div class="admin-topbar__dropdown" id="adminProfileDropdown">
            <a href="<?= url('logout.php') ?>">
                <i class="fas fa-sign-out-alt"></i>
                Wyloguj się
            </a>
        </div>
    </div>
</header>
