/**
 * Categories Manager - zarządzanie kategoriami i podkategoriami
 */

class CategoriesManager {
    constructor() {
        this.categories = [];
        this.init();
    }

    init() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Przyciski dodawania
        const addCategoryBtn = document.getElementById('add-category-btn');
        if (addCategoryBtn) {
            addCategoryBtn.addEventListener('click', () => this.showAddCategoryModal());
        }

        // Zamykanie modali
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                this.closeModal(e.target.id);
            }
        });
    }

    async loadCategories() {
        try {
            const response = await fetch('/Projekt/public/api/admin-categories.php');
            const data = await response.json();

            if (data.success) {
                this.categories = data.categories;
                this.renderCategories();
            } else {
                throw new Error(data.error || 'Błąd pobierania kategorii');
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            this.showNotification('Błąd pobierania kategorii: ' + error.message, 'error');
        }
    }

    renderCategories() {
        const container = document.getElementById('categories-list');
        if (!container) return;

        if (this.categories.length === 0) {
            container.innerHTML = '<div class="empty-state">Brak kategorii</div>';
            return;
        }

        container.innerHTML = this.categories.map(category => `
            <div class="category-card" data-category-id="${category.category_id}">
                <div class="category-header">
                    <div class="category-info">
                        <h3 class="category-name">${this.escapeHtml(category.name)}</h3>
                        ${category.description ? `<p class="category-description">${this.escapeHtml(category.description)}</p>` : ''}
                        <span class="category-meta">${category.subcategories_count} podkategorii</span>
                    </div>
                    <div class="category-actions">
                        <button class="action-btn edit-category-btn" 
                                data-category-id="${category.category_id}"
                                data-category-name="${this.escapeHtml(category.name)}"
                                data-category-description="${this.escapeHtml(category.description || '')}"
                                title="Edytuj kategorię">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-category-btn" 
                                data-category-id="${category.category_id}"
                                data-category-name="${this.escapeHtml(category.name)}"
                                title="Usuń kategorię">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="action-btn add-subcategory-btn" 
                                data-category-id="${category.category_id}"
                                data-category-name="${this.escapeHtml(category.name)}"
                                title="Dodaj podkategorię">
                            <i class="fas fa-plus"></i> Podkategoria
                        </button>
                    </div>
                </div>
                
                ${category.subcategories.length > 0 ? `
                    <div class="subcategories-list">
                        ${category.subcategories.map(sub => `
                            <div class="subcategory-item" data-subcategory-id="${sub.subcategory_id}">
                                <div class="subcategory-info">
                                    <span class="subcategory-name">${this.escapeHtml(sub.name)}</span>
                                    ${sub.description ? `<span class="subcategory-description">${this.escapeHtml(sub.description)}</span>` : ''}
                                </div>
                                <div class="subcategory-actions">
                                    <button class="action-btn-small edit-subcategory-btn" 
                                            data-subcategory-id="${sub.subcategory_id}"
                                            data-subcategory-name="${this.escapeHtml(sub.name)}"
                                            data-subcategory-description="${this.escapeHtml(sub.description || '')}"
                                            title="Edytuj">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn-small delete-subcategory-btn" 
                                            data-subcategory-id="${sub.subcategory_id}"
                                            data-subcategory-name="${this.escapeHtml(sub.name)}"
                                            title="Usuń">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        `).join('');

        this.attachCategoryActions();
    }

    attachCategoryActions() {
        // Edycja kategorii
        document.querySelectorAll('.edit-category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.categoryId);
                const name = btn.dataset.categoryName;
                const description = btn.dataset.categoryDescription;
                this.showEditCategoryModal(id, name, description);
            });
        });

        // Usuwanie kategorii
        document.querySelectorAll('.delete-category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.categoryId);
                const name = btn.dataset.categoryName;
                this.deleteCategory(id, name);
            });
        });

        // Dodawanie podkategorii
        document.querySelectorAll('.add-subcategory-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const categoryId = parseInt(btn.dataset.categoryId);
                const categoryName = btn.dataset.categoryName;
                this.showAddSubcategoryModal(categoryId, categoryName);
            });
        });

        // Edycja podkategorii
        document.querySelectorAll('.edit-subcategory-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.subcategoryId);
                const name = btn.dataset.subcategoryName;
                const description = btn.dataset.subcategoryDescription;
                this.showEditSubcategoryModal(id, name, description);
            });
        });

        // Usuwanie podkategorii
        document.querySelectorAll('.delete-subcategory-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.subcategoryId);
                const name = btn.dataset.subcategoryName;
                this.deleteSubcategory(id, name);
            });
        });
    }

    // ===== MODALS =====

    showAddCategoryModal() {
        const modal = document.getElementById('category-modal');
        const title = document.getElementById('category-modal-title');
        const nameInput = document.getElementById('category-name');
        const descInput = document.getElementById('category-description');
        const saveBtn = document.getElementById('save-category-btn');

        title.textContent = 'Dodaj kategorię';
        nameInput.value = '';
        descInput.value = '';
        modal.classList.add('active');

        saveBtn.onclick = () => this.saveCategory();
    }

    showEditCategoryModal(id, name, description) {
        const modal = document.getElementById('category-modal');
        const title = document.getElementById('category-modal-title');
        const nameInput = document.getElementById('category-name');
        const descInput = document.getElementById('category-description');
        const saveBtn = document.getElementById('save-category-btn');

        title.textContent = 'Edytuj kategorię';
        nameInput.value = name;
        descInput.value = description;
        modal.dataset.categoryId = id;
        modal.classList.add('active');

        saveBtn.onclick = () => this.saveCategory(id);
    }

    showAddSubcategoryModal(categoryId, categoryName) {
        const modal = document.getElementById('subcategory-modal');
        const title = document.getElementById('subcategory-modal-title');
        const nameInput = document.getElementById('subcategory-name');
        const descInput = document.getElementById('subcategory-description');
        const saveBtn = document.getElementById('save-subcategory-btn');

        title.textContent = `Dodaj podkategorię do: ${categoryName}`;
        nameInput.value = '';
        descInput.value = '';
        modal.dataset.categoryId = categoryId;
        delete modal.dataset.subcategoryId;
        modal.classList.add('active');

        saveBtn.onclick = () => this.saveSubcategory(categoryId);
    }

    showEditSubcategoryModal(id, name, description) {
        const modal = document.getElementById('subcategory-modal');
        const title = document.getElementById('subcategory-modal-title');
        const nameInput = document.getElementById('subcategory-name');
        const descInput = document.getElementById('subcategory-description');
        const saveBtn = document.getElementById('save-subcategory-btn');

        title.textContent = 'Edytuj podkategorię';
        nameInput.value = name;
        descInput.value = description;
        modal.dataset.subcategoryId = id;
        modal.classList.add('active');

        saveBtn.onclick = () => this.saveSubcategory(null, id);
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // ===== CRUD OPERATIONS =====

    async saveCategory(categoryId = null) {
        const nameInput = document.getElementById('category-name');
        const descInput = document.getElementById('category-description');
        
        const name = nameInput.value.trim();
        const description = descInput.value.trim();

        if (!name) {
            this.showNotification('Nazwa kategorii jest wymagana', 'warning');
            return;
        }

        try {
            const endpoint = categoryId 
                ? '/Projekt/public/api/admin-category-update.php'
                : '/Projekt/public/api/admin-category-add.php';

            const payload = { name, description };
            if (categoryId) {
                payload.category_id = categoryId;
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.closeModal('category-modal');
                await this.loadCategories();
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    async deleteCategory(categoryId, categoryName) {
        if (!confirm(`Czy na pewno chcesz usunąć kategorię "${categoryName}"?\n\nUWAGA: Zostaną usunięte wszystkie podkategorie tej kategorii!`)) {
            return;
        }

        try {
            const response = await fetch('/Projekt/public/api/admin-category-delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: categoryId })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                await this.loadCategories();
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    async saveSubcategory(categoryId = null, subcategoryId = null) {
        const nameInput = document.getElementById('subcategory-name');
        const descInput = document.getElementById('subcategory-description');
        
        const name = nameInput.value.trim();
        const description = descInput.value.trim();

        if (!name) {
            this.showNotification('Nazwa podkategorii jest wymagana', 'warning');
            return;
        }

        try {
            const endpoint = subcategoryId 
                ? '/Projekt/public/api/admin-subcategory-update.php'
                : '/Projekt/public/api/admin-subcategory-add.php';

            const payload = { name, description };
            if (subcategoryId) {
                payload.subcategory_id = subcategoryId;
            } else {
                payload.category_id = categoryId;
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.closeModal('subcategory-modal');
                await this.loadCategories();
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    async deleteSubcategory(subcategoryId, subcategoryName) {
        if (!confirm(`Czy na pewno chcesz usunąć podkategorię "${subcategoryName}"?`)) {
            return;
        }

        try {
            const response = await fetch('/Projekt/public/api/admin-subcategory-delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ subcategory_id: subcategoryId })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                await this.loadCategories();
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.showNotification('Błąd: ' + error.message, 'error');
        }
    }

    // ===== HELPER METHODS =====

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        
        let backgroundColor = '#2196F3';
        if (type === 'success') backgroundColor = '#4CAF50';
        if (type === 'error') backgroundColor = '#f44336';
        if (type === 'warning') backgroundColor = '#ff9800';
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${backgroundColor};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
            max-width: 400px;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Inicjalizacja i eksport
const categoriesManager = new CategoriesManager();
window.categoriesManager = categoriesManager;
