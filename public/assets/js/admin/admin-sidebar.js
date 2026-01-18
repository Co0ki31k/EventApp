/**
 * Admin Sidebar Navigation Handler
 * Handles active state switching for sidebar menu items
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get all sidebar menu items
    const menuItems = document.querySelectorAll('.admin-sidebar__menu-item');
    
    // Add click event listener to each menu item
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all menu items
            menuItems.forEach(menuItem => {
                menuItem.classList.remove('active');
            });
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Get panel name from data attribute
            const panelName = this.getAttribute('data-panel');
            
            // Log for debugging
            console.log('Switched to panel:', panelName);
            
            // Show/hide content panels
            switchPanel(panelName);
        });
    });
});

/**
 * Switch content panel
 * @param {string} panelName - Name of the panel to show
 */
function switchPanel(panelName) {
    // Hide all panels
    const panels = document.querySelectorAll('.admin-panel');
    panels.forEach(panel => {
        panel.style.display = 'none';
    });
    
    // Show selected panel
    const selectedPanel = document.getElementById(`panel-${panelName}`);
    if (selectedPanel) {
        selectedPanel.style.display = 'block';
        // If switched to dashboard, trigger stats reload
        if (panelName === 'dashboard' && typeof window.loadDashboardStats === 'function') {
            try {
                window.loadDashboardStats();
            } catch (e) {
                console.error('Failed to load dashboard stats:', e);
            }
        }
        // If switched to users, trigger users reload
        if (panelName === 'users' && window.usersManager) {
            try {
                window.usersManager.loadUsers();
            } catch (e) {
                console.error('Failed to load users:', e);
            }
        }
        // If switched to events, trigger events reload
        if (panelName === 'events' && window.eventsManager) {
            try {
                window.eventsManager.loadEvents();
            } catch (e) {
                console.error('Failed to load events:', e);
            }
        }
        // If switched to categories, trigger categories reload
        if (panelName === 'categories' && window.categoriesManager) {
            try {
                window.categoriesManager.loadCategories();
            } catch (e) {
                console.error('Failed to load categories:', e);
            }
        }
    }
}
