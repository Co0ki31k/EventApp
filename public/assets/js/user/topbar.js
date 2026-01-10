/* topbar.js
   Handles mode-switch buttons and profile edit trigger
*/

class TopbarManager {
    constructor() {
        this.modeButtons = document.querySelectorAll('.mode-btn');
        this.profileBtn = document.getElementById('profile-edit-btn');
        this.profileDropdown = document.getElementById('profile-dropdown');
        this.logoutBtn = document.getElementById('logout-btn');
        
        this.init();
    }
    
    init() {
        this.setupModeButtons();
        this.setupProfileDropdown();
        this.setupLogout();
    }
    
    setupModeButtons() {
        var self = this;
        
        this.modeButtons.forEach(function(btn){
            btn.addEventListener('click', function(){
                var mode = btn.getAttribute('data-mode');
                self.setActiveButton(btn);
                document.dispatchEvent(new CustomEvent('map:modeChange', { detail: { mode: mode } }));
            });
        });
        
        // default active first button
        if(this.modeButtons.length){ 
            this.setActiveButton(this.modeButtons[0]); 
        }
    }
    
    setActiveButton(btn) {
        this.modeButtons.forEach(function(b){ 
            b.classList.remove('active'); 
            b.setAttribute('aria-pressed','false'); 
        });
        if(btn){ 
            btn.classList.add('active'); 
            btn.setAttribute('aria-pressed','true'); 
        }
    }
    
    setupProfileDropdown() {
        var self = this;
        
        if(this.profileBtn && this.profileDropdown){
            this.profileBtn.addEventListener('click', function(e){
                e.stopPropagation();
                var isHidden = self.profileDropdown.classList.contains('profile-hidden');
                
                if(isHidden){
                    self.profileDropdown.classList.remove('profile-hidden');
                    self.loadProfileStats();
                } else {
                    self.profileDropdown.classList.add('profile-hidden');
                }
            });
        }
        
        // Zamknij dropdown po kliknięciu poza nim
        document.addEventListener('click', function(e){
            if(self.profileDropdown && !self.profileDropdown.contains(e.target) && e.target.id !== 'profile-edit-btn'){
                self.profileDropdown.classList.add('profile-hidden');
            }
        });
    }
    
    async loadProfileStats() {
        try {
            const response = await fetch('/Projekt/public/api/profile-stats.php');
            const data = await response.json();
            
            if(data.success) {
                document.getElementById('profile-username').textContent = data.username;
                document.getElementById('profile-email').textContent = data.email;
                document.getElementById('profile-events-count').textContent = data.events_count;
                document.getElementById('profile-friends-count').textContent = data.friends_count;
            } else {
                console.error('Failed to load profile stats');
            }
        } catch (error) {
            console.error('Error loading profile stats:', error);
        }
    }
    
    setupLogout() {
        if(this.logoutBtn){
            this.logoutBtn.addEventListener('click', function(){
                if(confirm('Czy na pewno chcesz się wylogować?')){
                    window.location.href = '/Projekt/public/logout.php';
                }
            });
        }
    }
}

// Inicjalizacja po załadowaniu strony
document.addEventListener('DOMContentLoaded', () => {
    window.topbar = new TopbarManager();
});
