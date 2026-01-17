<?php 
use Random\Engine\Secure;
?><!-- Admin Topbar -->
<header class="admin-topbar">
    <div class="admin-topbar__right">
        <div class="admin-topbar__profile">
            <i class="fas fa-user-circle" style="font-size: 2.5rem; color: #7f8c8d;"></i>
            <div class="admin-topbar__profile-info">
                <span><?= Security::escape($_SESSION['username']) ?></span>
            </div>
        </div>
    </div>
</header>
