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
		<h2>Zarządzanie kategoriami</h2>
		<p>Placeholder - lista kategorii</p>
	</div>

	<!-- Panel: reports -->
	<div class="admin-panel" id="panel-reports">
		<h2>Zarządzanie raportami</h2>
		<p>Placeholder - lista raportow</p>
	</div>
</main>
