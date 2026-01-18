<!-- Admin Sidebar Navigation -->
<aside class="admin-sidebar">
    <!-- Logo Section -->
    <div class="admin-sidebar__logo">
        <img src="<?= asset('img/logo.png') ?>" alt="Logo">
        <div class="admin-sidebar__logo-text">
            <span class="brand">EventApp</span>
            <span class="role">Administrator</span>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="admin-sidebar__menu">
        <div class="admin-sidebar__menu-section">
            <div class="admin-sidebar__menu-section-title">Menu główne</div>
            
            <a href="#" class="admin-sidebar__menu-item active" data-panel="dashboard">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="#" class="admin-sidebar__menu-item" data-panel="users">
                <i class="fas fa-users"></i>
                <span>Użytkownicy</span>
            </a>
            
            <a href="#" class="admin-sidebar__menu-item" data-panel="events">
                <i class="fas fa-calendar-alt"></i>
                <span>Wydarzenia</span>
            </a>
            
            <a href="#" class="admin-sidebar__menu-item" data-panel="categories">
                <i class="fas fa-tags"></i>
                <span>Kategorie</span>
            </a>
        </div>
    </nav>
</aside>
