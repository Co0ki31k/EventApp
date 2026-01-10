/**
 * Moduł obsługi znajomych użytkownika
 */

class FriendsManager {
    constructor() {
        this.container = document.getElementById('friends-container');
        this.countBadge = document.getElementById('friends-count');
        
        this.init();
    }
    
    init() {
        this.loadFriends();
        this.setupPanelListener();
    }
    
    /**
     * Nasłuchuj otwarcia panelu i przeładowuj znajomych
     */
    setupPanelListener() {
        var self = this;
        var friendsPanel = document.getElementById('panel-friends');
        
        if(!friendsPanel) return;

        // Use MutationObserver to detect when panel becomes visible
        var observer = new MutationObserver(function(mutations){
            mutations.forEach(function(mutation){
                if(mutation.type === 'attributes' && mutation.attributeName === 'class'){
                    var isPanelVisible = !friendsPanel.classList.contains('panel-hidden');
                    
                    if(isPanelVisible){
                        // Panel just became visible - always refresh data
                        self.loadFriends();
                    }
                }
            });
        });

        observer.observe(friendsPanel, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    
    /**
     * Pobierz znajomych z API
     */
    async loadFriends() {
        try {
            const response = await fetch('/Projekt/public/api/friends.php');
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to load friends');
            }
            
            if (data.success) {
                this.renderFriends(data.friends);
                this.updateCount(data.count);
            }
        } catch (error) {
            console.error('Error loading friends:', error);
            this.showError('Nie udało się załadować znajomych');
        }
    }
    
    /**
     * Renderuj listę znajomych
     */
    renderFriends(friends) {
        if (!friends || friends.length === 0) {
            this.container.innerHTML = `
                <div class="no-friends">
                    <div class="no-friends-icon">👥</div>
                    <p>Brak znajomych i zaproszeń</p>
                </div>
            `;
            return;
        }
        
        const html = friends.map(friend => this.createFriendHTML(friend)).join('');
        this.container.innerHTML = html;
        
        // Dodaj klasę scrollable jeśli jest więcej niż 5 znajomych
        if (friends.length > 5) {
            this.container.classList.add('scrollable');
        } else {
            this.container.classList.remove('scrollable');
        }
        
        // Dodaj event listeners do przycisków
        this.setupButtonListeners();
    }
    
    /**
     * Stwórz HTML dla pojedynczego znajomego
     */
    createFriendHTML(friend) {
        const icon = this.getFriendIcon(friend.status);
        const actions = this.getFriendActions(friend);
        
        return `
            <div class="friend-item ${friend.status}">
                <div class="friend-icon">${icon}</div>
                <div class="friend-content">
                    <div class="friend-name">${friend.username}</div>
                </div>
                <div class="friend-actions">
                    ${actions}
                </div>
            </div>
        `;
    }
    
    /**
     * Zwróć ikonę dla danego statusu
     */
    getFriendIcon(status) {
        const icons = {
            'sent': '📤',
            'received': '✉️',
            'accepted': '👤'
        };
        return icons[status] || '👤';
    }
    
    /**
     * Wygeneruj przyciski akcji (jako ikonki)
     */
    getFriendActions(friend) {
        switch (friend.status) {
            case 'sent':
                return `<button class="btn-action btn-cancel" data-action="cancel" data-friend-id="${friend.user_id}" title="Anuluj zaproszenie">✕</button>`;
            
            case 'received':
                return `
                    <button class="btn-action btn-accept" data-action="accept" data-friend-id="${friend.user_id}" title="Akceptuj">✓</button>
                    <button class="btn-action btn-reject" data-action="reject" data-friend-id="${friend.user_id}" title="Odrzuć">✕</button>
                `;
            
            case 'accepted':
                return `<button class="btn-action btn-remove" data-action="remove" data-friend-id="${friend.user_id}" title="Usuń znajomego">🗑️</button>`;
            
            default:
                return '';
        }
    }
    
    /**
     * Ustaw event listeners dla przycisków akcji
     */
    setupButtonListeners() {
        var self = this;
        var buttons = this.container.querySelectorAll('.btn-action');
        
        buttons.forEach(function(button){
            button.addEventListener('click', function(e){
                e.stopPropagation();
                var action = button.getAttribute('data-action');
                var friendId = parseInt(button.getAttribute('data-friend-id'));
                self.handleAction(action, friendId, button);
            });
        });
    }
    
    /**
     * Obsługa akcji na znajomym
     */
    async handleAction(action, friendId, button) {
        // Zablokuj przycisk
        button.disabled = true;
        var originalText = button.textContent;
        button.textContent = '...';
        
        try {
            const response = await fetch('/Projekt/public/api/friends-action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ action: action, friend_id: friendId })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Action failed');
            }
            
            if (data.success) {
                // Przeładuj listę znajomych
                this.loadFriends();
            } else {
                alert(data.message || 'Nie udało się wykonać akcji');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (error) {
            console.error('Error performing action:', error);
            alert('Wystąpił błąd podczas wykonywania akcji');
            button.disabled = false;
            button.textContent = originalText;
        }
    }
    
    /**
     * Zaktualizuj licznik znajomych
     */
    updateCount(count) {
        if (count > 0) {
            this.countBadge.textContent = count > 99 ? '99+' : count;
            this.countBadge.style.display = 'inline-block';
        } else {
            this.countBadge.style.display = 'none';
        }
    }
    
    /**
     * Pokaż komunikat o błędzie
     */
    showError(message) {
        this.container.innerHTML = `
            <div class="friends-error" style="text-align: center; padding: 20px; color: #e74c3c;">
                <p>❌ ${message}</p>
            </div>
        `;
    }
}

// Inicjalizacja po załadowaniu strony
document.addEventListener('DOMContentLoaded', () => {
    window.friendsManager = new FriendsManager();
});
