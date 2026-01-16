// Admin Panel Navigation & UI Controller

document.addEventListener('DOMContentLoaded', () => {
    initAdminNavigation();
    initProfileDropdown();
});

// Navigation between panels
function initAdminNavigation() {
    const navItems = document.querySelectorAll('.admin-nav-item');
    const panels = document.querySelectorAll('.admin-panel');
    const pageTitle = document.querySelector('.admin-page-title');
    
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const panelName = item.dataset.panel;
            
            // Update active nav item
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');
            
            // Switch panels
            panels.forEach(panel => panel.classList.remove('active'));
            const targetPanel = document.getElementById(`panel-${panelName}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
            
            // Update page title
            const navText = item.querySelector('span').textContent;
            if (pageTitle) {
                pageTitle.textContent = navText;
            }
        });
    });
}

// Profile dropdown toggle
function initProfileDropdown() {
    const profileBtn = document.getElementById('adminProfileBtn');
    const profileMenu = document.getElementById('adminProfileMenu');
    
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = profileMenu.style.display === 'block';
            profileMenu.style.display = isVisible ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.style.display = 'none';
            }
        });
        
        // Handle menu item clicks
        const menuItems = profileMenu.querySelectorAll('.admin-profile-menu-item[data-action]');
        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const action = item.dataset.action;
                
                // Handle different actions
                switch(action) {
                    case 'profile':
                        console.log('Open profile');
                        // TODO: Implement profile view
                        break;
                    case 'settings':
                        console.log('Open settings');
                        // TODO: Switch to settings panel
                        break;
                }
                
                profileMenu.style.display = 'none';
            });
        });
    }
}
