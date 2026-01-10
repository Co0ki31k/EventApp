<?php
// Profile dropdown panel (hidden by default)
?>
<div id="profile-dropdown" class="profile-dropdown profile-hidden">
    <div class="profile-dropdown-inner">
        <div class="profile-user-info">
            <div class="profile-name" id="profile-username">Ładowanie...</div>
            <div class="profile-email" id="profile-email"></div>
        </div>
        
        <div class="profile-stats">
            <div class="profile-stat">
                <div class="profile-stat-icon">🎉</div>
                <div class="profile-stat-content">
                    <div class="profile-stat-value" id="profile-events-count">-</div>
                    <div class="profile-stat-label">Wydarzenia</div>
                </div>
            </div>
            
            <div class="profile-stat">
                <div class="profile-stat-icon">👥</div>
                <div class="profile-stat-content">
                    <div class="profile-stat-value" id="profile-friends-count">-</div>
                    <div class="profile-stat-label">Znajomi</div>
                </div>
            </div>
        </div>
        
        <button id="logout-btn" class="btn-logout">Wyloguj się</button>
    </div>
</div>
