/**
 * Admin Topbar Handler
 * Handles profile dropdown toggle
 */

document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.getElementById('adminProfileBtn');
    const dropdown = document.getElementById('adminProfileDropdown');
    
    if (profileBtn && dropdown) {
        // Toggle dropdown on profile click
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
