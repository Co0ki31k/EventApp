<section id="panel-add-event" class="panel panel-hidden" aria-hidden="true">
    <header><h3>Dodaj wydarzenie</h3></header>
    <div class="panel-body panel-add-event">
        <?php
            $errors = [];
            $successMessage = null;
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
                require_once SRC_PATH . '/Controllers/EventsController.php';
                load_class('Security');
                $result = (new EventsController())->create();
                $errors = $result['errors'] ?? [];
                
                // PRG Pattern: redirect after successful POST to prevent duplicate submissions
                if ($result['success']) {
                    // Store success message in session for display after redirect
                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        @session_start();
                    }
                    $_SESSION['event_created'] = true;
                    $_SESSION['event_message'] = $result['message'] ?? 'Wydarzenie zostało utworzone.';
                    
                    // Redirect to same page (without POST data)
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
            
            // Check for success message from redirect
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            if (!empty($_SESSION['event_created'])) {
                $successMessage = $_SESSION['event_message'] ?? 'Wydarzenie zostało utworzone.';
                unset($_SESSION['event_created'], $_SESSION['event_message']);
            }
        ?>
        <form class="form-add-event" action="" method="post" novalidate>
            <?php if (!empty($successMessage)): ?>
                <script>
                    (function(){
                        // Reload markers on map after event created
                        try{ window.dispatchEvent(new CustomEvent('event:created')); } catch(e){}
                        // Remove green marker from map-controller
                        if(window.UserMapController && window.UserMapController._selectedMarker){
                            try{ window.UserMapController.markersLayer.removeLayer(window.UserMapController._selectedMarker); }catch(e){}
                            window.UserMapController._selectedMarker = null;
                        }
                    })();
                </script>
                <div class="alert alert-success"><?php echo Security::escape($successMessage); ?></div>
            <?php endif; ?>
            <?php if (!empty($errors) && is_array($errors)): ?>
                <script>
                    (function(){
                        var msgs = <?php echo json_encode(array_values($errors), JSON_UNESCAPED_UNICODE); ?> || [];
                        if (msgs.length) alert(msgs.join("\n"));
                    })();
                </script>
            <?php endif; ?>
            <div class="form-row">
                <label for="event-title">Tytuł wydarzenia</label>
                <div class="input-with-icon">
                    <input id="event-title" name="title" type="text" placeholder="Tytuł wydarzenia" />
                    <span class="input-icon icon-title" aria-hidden="true"></span>
                </div>
            </div>

            <div class="form-row">
                <label for="event-desc">Opis</label>
                <textarea id="event-desc" name="description" rows="4" placeholder="Opis"></textarea>
            </div>

            <div class="form-row">
                <label>Data i godzina</label>
                <div class="row-inline">
                    <div class="input-with-icon">
                        <input id="event-date" name="date" type="date" aria-label="Wybierz datę" />
                        <span class="input-icon icon-calendar" aria-hidden="true"></span>
                    </div>
                    <div class="input-with-icon">
                        <input id="event-time" name="time" type="time" aria-label="Wybierz godzinę" step="900" />
                        <span class="input-icon icon-clock" aria-hidden="true"></span>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label>Lokalizacja</label>
                    <div class="input-with-icon">
                        <button type="button" class="btn btn-primary btn-map" id="btn-point-on-map">Wskaż na mapie</button>
                        <span class="input-icon icon-map" aria-hidden="true"></span>
                        <input type="hidden" id="event-lat" name="latitude" value="" />
                        <input type="hidden" id="event-lng" name="longitude" value="" />
                    </div>
            </div>

            <div class="form-row">
                <label for="event-category">Kategoria</label>
                <div class="input-with-icon">
                    <?php
                        $categories = Database::query("SELECT category_id AS id, name FROM Categories ORDER BY name", []);
                    ?>
                    <select id="event-category" name="category">
                        <option value="">Wybierz kategorię</option>
                        <?php if ($categories && is_array($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo Security::escape((string)$cat['id']); ?>"><?php echo Security::escape((string)$cat['name']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>     
                    </select>
                    <span class="input-icon icon-category" aria-hidden="true"></span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-create">Utwórz</button>
            </div>
        </form>
    </div>
</section>
