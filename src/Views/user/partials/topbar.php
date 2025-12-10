<?php
// Topbar for user home — tryb wyświetlania (środek, nad mapą)
?>
<div class="user-topbar" role="region" aria-label="Topbar">
    <div class="mode-switch-wrapper">
        <div class="mode-group" role="tablist" aria-label="Tryby wyświetlania">
            <button class="mode-btn" data-mode="all" role="tab">🌐 Wszystko</button>
            <button class="mode-btn" data-mode="friends" role="tab">👥 Znajomi</button>
            <button class="mode-btn" data-mode="now" role="tab">🔥 Dzieje się teraz</button>
        </div>
    </div>
    <div class="profile-area">
        <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
        <button id="profile-edit-btn" class="profile-btn" aria-haspopup="dialog" aria-controls="profile-menu">
            <span class="profile-emoji" aria-hidden="true">👤</span>
            <span class="profile-name"><?php echo Security::escape($_SESSION['username'] ?? 'Profile'); ?></span>
            <span class="profile-arrow js-profile-toggle" aria-hidden="true">▾</span>
        </button>
    </div>
</div>

