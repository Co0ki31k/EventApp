/**
 * Admin Panel - Zarządzanie wydarzeniami
 */

class EventsManager {
    constructor() {
        this.currentPage = 1;
        this.searchTerm = '';
        this.init();
    }

    init() {
        // Nasłuchuj na przełączenie do panelu wydarzeń
        document.addEventListener('panelSwitched', (e) => {
            if (e.detail.panel === 'events') {
                this.loadEvents();
            }
        });

        // Wyszukiwanie
        const searchInput = document.getElementById('event-search');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                this.searchTerm = e.target.value.trim();
                searchTimeout = setTimeout(() => {
                    this.currentPage = 1;
                    this.loadEvents();
                }, 500);
            });
        }
    }

    async loadEvents() {
        const tbody = document.getElementById('events-table-body');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="7" class="loading-cell">Ładowanie danych...</td></tr>';

        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: 20
            });

            if (this.searchTerm) {
                params.append('search', this.searchTerm);
            }

            const response = await fetch(`/Projekt/public/api/admin-events.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderEvents(data.events);
                this.renderPagination(data.pagination);
            } else {
                throw new Error(data.error || 'Błąd pobierania wydarzeń');
            }
        } catch (error) {
            console.error('Error loading events:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="error-cell">Błąd: ${error.message}</td></tr>`;
        }
    }

    renderEvents(events) {
        const tbody = document.getElementById('events-table-body');
        if (!tbody) return;

        if (events.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="empty-cell">Brak wydarzeń</td></tr>';
            return;
        }

        tbody.innerHTML = events.map(event => `
            <tr data-event-id="${event.event_id}">
                <td>${event.event_id}</td>
                <td>${this.escapeHtml(event.title)}</td>
                <td>${this.escapeHtml(event.category || 'Brak')}</td>
                <td>${this.formatDateTime(event.start_datetime)}</td>
                <td>${this.formatDateTime(event.end_datetime)}</td>
                <td>${this.escapeHtml(event.created_by_username || 'Nieznany')}</td>
                <td class="actions-cell">
                    ${this.renderActions(event)}
                </td>
            </tr>
        `).join('');

        // Dodaj event listenery do akcji
        this.attachActionListeners();
    }

    renderActions(event) {
        // Sprawdź czy wydarzenie jeszcze trwa
        const now = new Date();
        const endDate = new Date(event.end_datetime);
        const isOngoing = endDate >= now;
        
        if (!isOngoing) {
            return '<span class="text-muted" style="font-size: 0.85rem;">Zakończone</span>';
        }
        
        return `
            <button class="action-btn delete-btn" 
                    data-event-id="${event.event_id}" 
                    data-event-title="${this.escapeHtml(event.title)}"
                    title="Usuń wydarzenie">
                <i class="fas fa-trash"></i> Usuń
            </button>
        `;
    }

    attachActionListeners() {
        // Usuwanie wydarzenia
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const eventId = parseInt(btn.dataset.eventId);
                const eventTitle = btn.dataset.eventTitle;
                this.deleteEvent(eventId, eventTitle);
            });
        });
    }

    async deleteEvent(eventId, eventTitle) {
        if (!confirm(`Czy na pewno chcesz USUNĄĆ wydarzenie "${eventTitle}"?\n\nTa operacja jest nieodwracalna!`)) {
            return;
        }

        // Dodatkowe potwierdzenie
        const confirmation = prompt(`Wpisz tytuł wydarzenia "${eventTitle}" aby potwierdzić usunięcie:`);
        if (confirmation !== eventTitle) {
            this.showNotification('Usunięcie anulowane - tytuł nie pasuje', 'warning');
            return;
        }

        try {
            const response = await fetch('/Projekt/public/api/admin-delete-event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    event_id: eventId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Wydarzenie zostało usunięte pomyślnie', 'success');
                this.loadEvents(); // Odśwież listę
            } else {
                throw new Error(data.error || 'Błąd usuwania wydarzenia');
            }
        } catch (error) {
            console.error('Error deleting event:', error);
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    renderPagination(pagination) {
        const container = document.getElementById('events-pagination');
        if (!container) return;

        if (pagination.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        const buttons = [];

        // Przycisk poprzednia strona
        if (pagination.page > 1) {
            buttons.push(`<button class="page-btn" data-page="${pagination.page - 1}">Poprzednia</button>`);
        }

        // Numery stron
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (
                i === 1 || 
                i === pagination.totalPages || 
                (i >= pagination.page - 2 && i <= pagination.page + 2)
            ) {
                const active = i === pagination.page ? 'active' : '';
                buttons.push(`<button class="page-btn ${active}" data-page="${i}">${i}</button>`);
            } else if (
                i === pagination.page - 3 || 
                i === pagination.page + 3
            ) {
                buttons.push(`<span class="page-dots">...</span>`);
            }
        }

        // Przycisk następna strona
        if (pagination.page < pagination.totalPages) {
            buttons.push(`<button class="page-btn" data-page="${pagination.page + 1}">Następna</button>`);
        }

        container.innerHTML = `
            <div class="pagination-info">
                Strona ${pagination.page} z ${pagination.totalPages} (${pagination.total} wydarzeń)
            </div>
            <div class="pagination-buttons">
                ${buttons.join('')}
            </div>
        `;

        // Event listenery dla przycisków paginacji
        container.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.currentPage = parseInt(btn.dataset.page);
                this.loadEvents();
            });
        });
    }

    showNotification(message, type = 'info') {
        // Prosta notyfikacja (możesz użyć bardziej zaawansowanego systemu)
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#ff9800'};
            color: white;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    formatDateTime(datetime) {
        if (!datetime) return 'Brak';
        const date = new Date(datetime);
        return date.toLocaleString('pl-PL', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Inicjalizuj manager i wyeksportuj do window
const eventsManager = new EventsManager();
window.eventsManager = eventsManager;
