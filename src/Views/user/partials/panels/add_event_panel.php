<section id="panel-add-event" class="panel panel-hidden" aria-hidden="true">
    <header><h3>Dodaj wydarzenie</h3></header>
    <div class="panel-body panel-add-event">
        <form class="form-add-event" action="#" method="post" novalidate>
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
                        <input id="event-time" name="time" type="time" aria-label="Wybierz godzinę" />
                        <span class="input-icon icon-clock" aria-hidden="true"></span>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label>Lokalizacja</label>
                    <div class="input-with-icon">
                        <button type="button" class="btn btn-map" id="btn-point-on-map">Wskaż na mapie</button>
                        <span class="input-icon icon-map" aria-hidden="true"></span>
            
                    </div>
            </div>

            <div class="form-row">
                <label for="event-category">Kategoria</label>
                <div class="input-with-icon">
                    <select id="event-category" name="category">
                        <option value="">Wybierz kategorię</option>
                        <option value="sport">Sport</option>
                        <option value="cafe">Kawiarnia</option>
                        <option value="walk">Spacer</option>
                        <option value="cinema">Kino</option>
                        <option value="run">Bieg</option>
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
