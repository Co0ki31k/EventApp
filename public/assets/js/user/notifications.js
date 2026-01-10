/**
 * Moduł obsługi powiadomień użytkownika
 */

class NotificationsManager {
    constructor() {
        this.container = document.getElementById('notifications-container');
        this.countBadge = document.getElementById('notification-count');
        
        this.init();
    }
    
    init() {
        this.loadNotifications();
        this.setupPanelListener();
    }
    
    /**
     * Nasłuchuj otwarcia panelu i przeładowuj powiadomienia
     */
    setupPanelListener() {
        var self = this;
        var notificationsPanel = document.getElementById('panel-notifications');
        
        if(!notificationsPanel) return;

        // Use MutationObserver to detect when panel becomes visible
        var observer = new MutationObserver(function(mutations){
            mutations.forEach(function(mutation){
                if(mutation.type === 'attributes' && mutation.attributeName === 'class'){
                    var isPanelVisible = !notificationsPanel.classList.contains('panel-hidden');
                    
                    if(isPanelVisible){
                        // Panel just became visible - always refresh data
                        self.loadNotifications();
                    }
                }
            });
        });

        observer.observe(notificationsPanel, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    
    /**
     * Pobierz powiadomienia z API
     */
    async loadNotifications() {
        try {
            const response = await fetch('/Projekt/public/api/notifications.php');
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to load notifications');
            }
            
            if (data.success) {
                this.renderNotifications(data.notifications);
                this.updateCount(data.count);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError('Nie udało się załadować powiadomień');
        }
    }
    
    /**
     * Renderuj listę powiadomień
     */
    renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            this.container.innerHTML = `
                <div class="no-notifications">
                    <div class="no-notifications-icon">🔔</div>
                    <p>Brak nowych powiadomień</p>
                </div>
            `;
            return;
        }
        
        const html = notifications.map(notif => this.createNotificationHTML(notif)).join('');
        this.container.innerHTML = html;
    }
    
    /**
     * Stwórz HTML dla pojedynczego powiadomienia
     */
    createNotificationHTML(notif) {
        const timeAgo = this.getTimeAgo(notif.timestamp);
        const icon = this.getNotificationIcon(notif.type);
        const message = this.getNotificationMessage(notif);
        const urgentClass = notif.type === 'event_starting_soon' ? 'urgent' : '';
        
        return `
            <div class="notification-item ${notif.type} ${urgentClass}">
                <div class="notification-icon">${icon}</div>
                <div class="notification-content">
                    <div class="notification-message">${message}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
            </div>
        `;
    }
    
    /**
     * Zwróć ikonę dla danego typu powiadomienia
     */
    getNotificationIcon(type) {
        const icons = {
            'friend_request_sent': '📤',
            'friend_request_received': '👥',
            'event_joined': '✅',
            'event_created': '🎉',
            'new_participant': '👤',
            'event_starting_soon': '⏰',
            'event_ongoing': '🔴'
        };
        return icons[type] || '🔔';
    }
    
    /**
     * Wygeneruj wiadomość dla powiadomienia
     */
    getNotificationMessage(notif) {
        switch (notif.type) {
            case 'friend_request_sent':
                return `Wysłano zaproszenie do ${notif.username}`;
            
            case 'friend_request_received':
                return `${notif.username} wysłał Ci zaproszenie do znajomych`;
            
            case 'event_joined':
                return `Zapisano na wydarzenie: ${notif.event_title}`;
            
            case 'event_created':
                return `Utworzono nowe wydarzenie: ${notif.event_title}`;
            
            case 'new_participant':
                return `${notif.username} dołączył do wydarzenia "${notif.event_title}"`;
            
            case 'event_starting_soon':
                const minutesText = notif.minutes_until == 1 ? 'minutę' 
                    : (notif.minutes_until < 5 ? 'minuty' : 'minut');
                return `Wydarzenie "${notif.event_title}" za ${notif.minutes_until} ${minutesText}`;
            
            case 'event_ongoing':
                const remainingText = notif.minutes_remaining == 1 ? 'minutę' 
                    : (notif.minutes_remaining < 5 ? 'minuty' : 'minut');
                return `Trwa wydarzenie "${notif.event_title}" (pozostało ${notif.minutes_remaining} ${remainingText})`;
            
            default:
                return 'Nowe powiadomienie';
        }
    }
    
    /**
     * Zaktualizuj licznik powiadomień
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
     * Oblicz "ile czasu temu"
     */
    getTimeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diffMs = now - time;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'Przed chwilą';
        if (diffMins < 60) return `${diffMins} min temu`;
        if (diffHours < 24) return `${diffHours} godz. temu`;
        if (diffDays === 1) return 'Wczoraj';
        if (diffDays < 7) return `${diffDays} dni temu`;
        
        return time.toLocaleDateString('pl-PL');
    }
    
    /**
     * Pokaż komunikat o błędzie
     */
    showError(message) {
        this.container.innerHTML = `
            <div class="notification-error" style="text-align: center; padding: 20px; color: #e74c3c;">
                <p>❌ ${message}</p>
            </div>
        `;
    }
}

// Inicjalizacja po załadowaniu strony
document.addEventListener('DOMContentLoaded', () => {
    window.notificationsManager = new NotificationsManager();
});

