<?php
// Settings panel - notification preferences
?>
<section id="panel-settings" class="panel panel-hidden" aria-hidden="true">
    <header><h3>Ustawienia</h3></header>
    <div class="panel-body">
        <div class="settings-section">
            <h4>Preferencje powiadomień</h4>
            <p class="settings-description">Wybierz, które powiadomienia chcesz otrzymywać</p>
            
            <form id="notification-settings-form" class="notification-settings-form">
                <div class="settings-group">
                    <h5>Znajomi</h5>
                    
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" name="friend_request_sent" id="pref-friend-request-sent" checked>
                            <span class="setting-label">Wysłane zaproszenia do znajomych</span>
                        </label>
                        <p class="setting-description">Powiadomienia o zaproszeniach, które wysłałeś do innych użytkowników</p>
                    </div>
                    
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" name="friend_request_received" id="pref-friend-request-received" checked>
                            <span class="setting-label">Otrzymane zaproszenia do znajomych</span>
                        </label>
                        <p class="setting-description">Powiadomienia o nowych zaproszeniach od innych użytkowników</p>
                    </div>
                </div>

                <div class="settings-group">
                    <h5>Wydarzenia</h5>
                    
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" name="event_joined" id="pref-event-joined" checked>
                            <span class="setting-label">Zapisanie się do wydarzenia</span>
                        </label>
                        <p class="setting-description">Powiadomienia o pomyślnym zapisaniu się do wydarzenia</p>
                    </div>
                    
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" name="event_created" id="pref-event-created" checked>
                            <span class="setting-label">Utworzone wydarzenia</span>
                        </label>
                        <p class="setting-description">Powiadomienia o wydarzeniach, które utworzyłeś</p>
                    </div>
                    
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" name="event_starting_soon" id="pref-event-starting-soon" checked>
                            <span class="setting-label">Wydarzenia rozpoczynające się wkrótce</span>
                        </label>
                        <p class="setting-description">Powiadomienia o wydarzeniach rozpoczynających się w ciągu 15 minut</p>
                    </div>
                    
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" name="event_ongoing" id="pref-event-ongoing" checked>
                            <span class="setting-label">Trwające wydarzenia</span>
                        </label>
                        <p class="setting-description">Powiadomienia o wydarzeniach, które odbywają się w tej chwili</p>
                    </div>
                </div>

                <div class="settings-actions">
                    <button type="submit" class="btn btn-primary">Zapisz ustawienia</button>
                    <button type="button" class="btn btn-secondary" id="reset-settings-btn">Resetuj do domyślnych</button>
                </div>
            </form>
            
            <div id="settings-message" class="settings-message" style="display: none;"></div>
        </div>
    </div>
</section>
