/**
 * Admin Panel - Zarządzanie użytkownikami
 */

class UsersManager {
    constructor() {
        this.currentPage = 1;
        this.searchTerm = '';
        this.init();
    }

    init() {
        // Nasłuchuj na przełączenie do panelu użytkowników
        document.addEventListener('panelSwitched', (e) => {
            if (e.detail.panel === 'users') {
                this.loadUsers();
            }
        });

        // Wyszukiwanie
        const searchInput = document.getElementById('user-search');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                this.searchTerm = e.target.value.trim();
                searchTimeout = setTimeout(() => {
                    this.currentPage = 1;
                    this.loadUsers();
                }, 500);
            });
        }
    }

    async loadUsers() {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="9" class="loading-cell">Ładowanie danych...</td></tr>';

        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: 20
            });

            if (this.searchTerm) {
                params.append('search', this.searchTerm);
            }

            const response = await fetch(`/Projekt/public/api/admin-users.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderUsers(data.users);
                this.renderPagination(data.pagination);
            } else {
                throw new Error(data.error || 'Błąd pobierania użytkowników');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            tbody.innerHTML = `<tr><td colspan="9" class="error-cell">Błąd: ${error.message}</td></tr>`;
        }
    }

    renderUsers(users) {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-cell">Brak użytkowników</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(user => `
            <tr data-user-id="${user.user_id}">
                <td>${user.user_id}</td>
                <td>${this.escapeHtml(user.username)}</td>
                <td>${this.escapeHtml(user.email)}</td>
                <td>
                    <span class="role-badge role-${user.role}">${user.role}</span>
                </td>
                <td>${user.last_login ? this.formatDate(user.last_login) : 'Nigdy'}</td>
                <td class="actions-cell">
                    ${this.renderActions(user)}
                </td>
            </tr>
        `).join('');

        // Dodaj event listenery do akcji
        this.attachActionListeners();
    }

    renderActions(user) {
        // Nie pokazuj akcji dla aktualnie zalogowanego admina
        const currentUserId = parseInt(document.body.dataset.userId || '0');
        if (user.user_id === currentUserId) {
            return '<span class="current-user-label">Ty</span>';
        }

        const actions = [];

        // Zmiana roli tylko dla user <-> admin
        if (user.role === 'user') {
            actions.push(`
                <button class="action-btn promote-btn" 
                        data-user-id="${user.user_id}" 
                        data-username="${this.escapeHtml(user.username)}"
                        title="Zmień na admina">
                    <i class="fas fa-user-shield"></i> Admin
                </button>
            `);
        } else if (user.role === 'admin') {
            actions.push(`
                <button class="action-btn demote-btn" 
                        data-user-id="${user.user_id}" 
                        data-username="${this.escapeHtml(user.username)}"
                        title="Zmień na użytkownika">
                    <i class="fas fa-user"></i> User
                </button>
            `);
        }

        // Usunięcie użytkownika
        actions.push(`
            <button class="action-btn delete-btn" 
                    data-user-id="${user.user_id}" 
                    data-username="${this.escapeHtml(user.username)}"
                    title="Usuń użytkownika">
                <i class="fas fa-trash"></i>
            </button>
        `);

        return actions.join('');
    }

    attachActionListeners() {
        // Awansowanie do admina
        document.querySelectorAll('.promote-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = parseInt(btn.dataset.userId);
                const username = btn.dataset.username;
                this.changeRole(userId, username, 'admin');
            });
        });

        // Degradacja do użytkownika
        document.querySelectorAll('.demote-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = parseInt(btn.dataset.userId);
                const username = btn.dataset.username;
                this.changeRole(userId, username, 'user');
            });
        });

        // Usunięcie użytkownika
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = parseInt(btn.dataset.userId);
                const username = btn.dataset.username;
                this.deleteUser(userId, username);
            });
        });
    }

    async changeRole(userId, username, newRole) {
        const action = newRole === 'admin' ? 'awansować' : 'degradować';
        const roleLabel = newRole === 'admin' ? 'admina' : 'użytkownika';

        if (!confirm(`Czy na pewno chcesz ${action} użytkownika "${username}" do roli ${roleLabel}?`)) {
            return;
        }

        try {
            const response = await fetch('/Projekt/public/api/admin-change-role.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    new_role: newRole
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Rola została zmieniona pomyślnie', 'success');
                this.loadUsers(); // Odśwież listę
            } else {
                throw new Error(data.error || 'Błąd zmiany roli');
            }
        } catch (error) {
            console.error('Error changing role:', error);
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    async deleteUser(userId, username) {
        if (!confirm(`Czy na pewno chcesz USUNĄĆ konto użytkownika "${username}"?\n\nTa operacja jest nieodwracalna!`)) {
            return;
        }

        // Dodatkowe potwierdzenie
        const confirmation = prompt(`Wpisz nazwę użytkownika "${username}" aby potwierdzić usunięcie:`);
        if (confirmation !== username) {
            this.showNotification('Usunięcie anulowane - nazwa nie pasuje', 'warning');
            return;
        }

        try {
            const response = await fetch('/Projekt/public/api/admin-delete-user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Użytkownik został usunięty', 'success');
                this.loadUsers(); // Odśwież listę
            } else {
                throw new Error(data.error || 'Błąd usuwania użytkownika');
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    renderPagination(pagination) {
        const container = document.getElementById('users-pagination');
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
                Strona ${pagination.page} z ${pagination.totalPages} (${pagination.total} użytkowników)
            </div>
            <div class="pagination-buttons">
                ${buttons.join('')}
            </div>
        `;

        // Event listenery dla przycisków paginacji
        container.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.currentPage = parseInt(btn.dataset.page);
                this.loadUsers();
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

    formatDate(dateString) {
        if (!dateString) return '—';
        const date = new Date(dateString);
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

// Inicjalizacja po załadowaniu DOM
document.addEventListener('DOMContentLoaded', () => {
    window.usersManager = new UsersManager();
});
