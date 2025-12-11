<?php
// Left vertical sidebar with icons that expand panels
?>
<aside class="user-sidebar" aria-label="Sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo" aria-hidden="true">
            <img src="<?= asset('img/logo.png') ?>" alt="Logo" />
        </div>

        <div class="sidebar-action">
            <button class="sidebar-icon primary" data-panel="add-event" title="Dodaj wydarzenie" role="tab">+</button>
        </div>

        <ul class="sidebar-group sidebar-main" role="tablist">
            <li><button class="sidebar-icon" data-panel="calendar" title="Kalendarz" role="tab">📅</button></li>
            <li><button class="sidebar-icon" data-panel="messages" title="Wiadomości" role="tab">💬</button></li>
            <li><button class="sidebar-icon" data-panel="notifications" title="Powiadomienia" role="tab">🔔</button></li>
        </ul>

        <ul class="sidebar-group sidebar-secondary" role="tablist">
            <li><button class="sidebar-icon" data-panel="friends" title="Znajomi" role="tab">👥</button></li>
            <li><button class="sidebar-icon" data-panel="filters" title="Filtr mapy" role="tab">⚡</button></li>
        </ul>

        <div class="sidebar-settings">
            <button class="sidebar-icon" data-panel="settings" title="Ustawienia" role="tab">⚙️</button>
        </div>

        <div class="sidebar-panels" aria-live="polite">
            <?php
            // Panels moved into the sidebar so the left side becomes one big expandable panel
            view('user/partials/panels/add_event_panel');
            view('user/partials/panels/calendar_panel');
            view('user/partials/panels/messages_panel');
            view('user/partials/panels/notifications_panel');
            view('user/partials/panels/friends_panel');
            view('user/partials/panels/filters_panel');
            view('user/partials/panels/settings_panel');
            ?>
        </div>
    </div>
</aside>

