<?php
// Filters panel — rozsuwany panel z filtrami mapy
// Get current filter values from URL if they exist
$currentCategory = $_GET['category'] ?? '';
$currentStartDate = $_GET['start_date'] ?? '';
?>
<section id="panel-filters" class="panel panel-hidden" aria-hidden="true">
    <header><h3>Filtruj wydarzenia</h3></header>
    <div class="panel-body panel-filters">
        <form class="form-filters" id="form-filters">
            <div class="form-row">
                <label for="filter-category">Kategoria</label>
                <div class="input-with-icon">
                    <?php
                        require_once SRC_PATH . '/classes/Database.php';
                        $categories = Database::query("SELECT category_id AS id, name FROM Categories ORDER BY name", []);
                    ?>
                    <select id="filter-category" name="category">
                        <option value="">Wszystkie kategorie</option>
                        <?php if ($categories && is_array($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo Security::escape((string)$cat['id']); ?>" <?php echo $currentCategory == $cat['id'] ? 'selected' : ''; ?>><?php echo Security::escape((string)$cat['name']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>     
                    </select>
                    <span class="input-icon icon-category" aria-hidden="true"></span>
                </div>
            </div>

            <div class="form-row">
                <label>Data wydarzenia</label>
                <div class="input-with-icon">
                    <input id="filter-start-date" name="start_date" type="date" aria-label="Data wydarzenia" value="<?php echo Security::escape($currentStartDate); ?>" />
                    <span class="input-icon icon-calendar" aria-hidden="true"></span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="btn-reset-filters">Resetuj</button>
                <button type="submit" class="btn btn-primary">Zastosuj</button>
            </div>
        </form>
    </div>
</section>

<script>
(function(){
    var resetBtn = document.getElementById('btn-reset-filters');
    var form = document.getElementById('form-filters');
    
    if(resetBtn && form){
        resetBtn.addEventListener('click', function(){
            // Clear all form fields
            form.reset();
            // Remove URL parameters and reload page
            window.location.href = window.location.pathname;
        });
    }
})();
</script>
