<?php 
use Random\Engine\Secure;
?><!-- Admin Topbar -->
<header class="admin-topbar">
    <div class="admin-topbar-right">
        <div class="admin-profile-dropdown">
            <button class="admin-profile-btn" id="adminProfileBtn">
                <span class="admin-profile-name"><?= Security::escape($_SESSION['username']) ?></span>
            </button>
            
            <div class="admin-profile-menu" id="adminProfileMenu" style="display: none;">
                <ul class="admin-profile-menu-list">
                    <li>
                        <a href="<?= url('logout.php') ?>" class="admin-profile-menu-item logout">
                            <span>Wyloguj</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
