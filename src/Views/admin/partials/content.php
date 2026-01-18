<!-- Main Content Area for Admin Panels -->
<main class="admin-content">
	<!-- Panel: Dashboard -->
	<div class="admin-panel active" id="panel-dashboard">

		<!-- Simplified dashboard tiles (placeholders) - preserve classes for JS -->
		<div class="dashboard-grid">
			<div class="dashboard-tile">
				<div class="dashboard-tile__header">
					<h3 class="dashboard-tile__title">Wydarzenia Ogółem</h3>
					<i class="fas fa-calendar-check dashboard-tile__icon"></i>
				</div>
				<div class="dashboard-tile__value">—</div>
				<div class="dashboard-tile__chart"></div>
			</div>

			<div class="dashboard-tile">
				<div class="dashboard-tile__header">
					<h3 class="dashboard-tile__title">Wydarzenia Ten Miesiąc</h3>
					<i class="fas fa-calendar-plus dashboard-tile__icon"></i>
				</div>
				<div class="dashboard-tile__value">—</div>
				<div class="dashboard-tile__chart"></div>
			</div>

			<div class="dashboard-tile">
				<div class="dashboard-tile__header">
					<h3 class="dashboard-tile__title">Wydarzenia Dziś</h3>
					<i class="fas fa-calendar-day dashboard-tile__icon"></i>
				</div>
				<div class="dashboard-tile__value">—</div>
				<div class="dashboard-tile__chart-center"></div>
			</div>

			<div class="dashboard-tile">
				<div class="dashboard-tile__header">
					<h3 class="dashboard-tile__title">Uczestnicy Ogółem</h3>
					<i class="fas fa-users dashboard-tile__icon"></i>
				</div>
				<div class="dashboard-tile__value">—</div>
				<div class="dashboard-tile__chart"></div>
			</div>

			<div class="dashboard-tile">
				<div class="dashboard-tile__header">
					<h3 class="dashboard-tile__title">Uczestnicy Ten Miesiąc</h3>
					<i class="fas fa-user-plus dashboard-tile__icon"></i>
				</div>
				<div class="dashboard-tile__value">—</div>
				<div class="dashboard-tile__chart"></div>
			</div>

			<div class="dashboard-tile">
				<div class="dashboard-tile__header">
					<h3 class="dashboard-tile__title">Uczestnicy Dziś</h3>
					<i class="fas fa-user-clock dashboard-tile__icon"></i>
				</div>
				<div class="dashboard-tile__value">—</div>
				<div class="dashboard-tile__chart-center"></div>
			</div>

		</div>
	</div>
	
	<!-- Panel: Users -->
	<div class="admin-panel" id="panel-users">
		<div class="panel-header">
			<h2>Zarządzanie użytkownikami</h2>
			<div class="search-box">
				<input type="text" id="user-search" placeholder="Wyszukaj użytkownika po nazwie...">
				<i class="fas fa-search"></i>
			</div>
		</div>

		<div class="users-table-container">
			<table class="users-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Username</th>
						<th>Email</th>
						<th>Rola</th>
						<th>Ostatnie logowanie</th>
						<th>Akcje</th>
					</tr>
				</thead>
				<tbody id="users-table-body">
					<tr>
						<td colspan="6" class="loading-cell">Ładowanie danych...</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="pagination" id="users-pagination">
			<!-- Paginacja będzie generowana przez JS -->
		</div>
	</div>
	
	<!-- Panel: Events -->
	<div class="admin-panel" id="panel-events">
		<div class="panel-header">
			<h2>Zarządzanie wydarzeniami</h2>
			<div class="search-box">
				<input type="text" id="event-search" placeholder="Wyszukaj wydarzenie...">
				<i class="fas fa-search"></i>
			</div>
		</div>

		<div class="events-table-container">
			<table class="events-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Tytuł</th>
						<th>Kategoria</th>
						<th>Start</th>
						<th>Koniec</th>
						<th>Utworzony przez</th>
						<th>Akcje</th>
					</tr>
				</thead>
				<tbody id="events-table-body">
					<tr>
						<td colspan="7" class="loading-cell">Ładowanie danych...</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="pagination" id="events-pagination">
			<!-- Paginacja będzie generowana przez JS -->
		</div>
	</div>
	
	<!-- Panel: Categories -->
	<div class="admin-panel" id="panel-categories">
		<div class="panel-header">
			<h2 class="panel-title">Zarządzanie kategoriami</h2>
			<button id="add-category-btn" class="btn-primary">
				<i class="fas fa-plus"></i> Dodaj kategorię
			</button>
		</div>

		<div id="categories-list" class="categories-container">
			<!-- Kategorie będą załadowane przez JavaScript -->
		</div>

		<!-- Modal dodawania/edycji kategorii -->
		<div id="category-modal" class="modal-overlay">
			<div class="modal-content">
				<div class="modal-header">
					<h3 id="category-modal-title">Dodaj kategorię</h3>
					<button class="modal-close" onclick="window.categoriesManager.closeModal('category-modal')">
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="category-name">Nazwa kategorii *</label>
						<input type="text" id="category-name" class="form-input" placeholder="np. Sport, Muzyka">
					</div>
					<div class="form-group">
						<label for="category-description">Opis (opcjonalnie)</label>
						<textarea id="category-description" class="form-input" rows="3" placeholder="Krótki opis kategorii"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn-secondary" onclick="window.categoriesManager.closeModal('category-modal')">Anuluj</button>
					<button id="save-category-btn" class="btn-primary">Zapisz</button>
				</div>
			</div>
		</div>

		<!-- Modal dodawania/edycji podkategorii -->
		<div id="subcategory-modal" class="modal-overlay">
			<div class="modal-content">
				<div class="modal-header">
					<h3 id="subcategory-modal-title">Dodaj podkategorię</h3>
					<button class="modal-close" onclick="window.categoriesManager.closeModal('subcategory-modal')">
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="subcategory-name">Nazwa podkategorii *</label>
						<input type="text" id="subcategory-name" class="form-input" placeholder="np. Piłka nożna, Jazz">
					</div>
					<div class="form-group">
						<label for="subcategory-description">Opis (opcjonalnie)</label>
						<textarea id="subcategory-description" class="form-input" rows="3" placeholder="Krótki opis podkategorii"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn-secondary" onclick="window.categoriesManager.closeModal('subcategory-modal')">Anuluj</button>
					<button id="save-subcategory-btn" class="btn-primary">Zapisz</button>
				</div>
			</div>
		</div>
	</div>
</main>
