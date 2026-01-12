/* guest-map-controller.js
   Uproszczony kontroler mapy dla gości - tylko odczyt wydarzeń
*/
(function(){
    window.GuestMapController = {
        map: null,
        markersLayer: null,
        pendingEvents: [], // Lista zapisanych wydarzeń
        init: function(opts){
            // avoid double initialization
            if(this._initialized){
                console.log('GuestMapController already initialized');
                return;
            }

            console.log('GuestMapController init', opts);
            if(typeof L === 'undefined'){
                console.error('Leaflet (L) is not loaded. Ensure Leaflet JS is included.');
                return;
            }
            
            // Default center: Łódź
            var center = (opts && opts.center) ? opts.center : [51.7592, 19.4550];
            var zoom = (opts && opts.zoom) ? opts.zoom : 13;

            var mapEl = document.getElementById('guest-map');
            if(!mapEl){ console.error('Map element #guest-map not found'); return; }

            // create map - tylko odczyt dla gości (bez kontrolek zoom)
            this.map = L.map('guest-map', { zoomControl: false }).setView(center, zoom);

            // use CartoDB Positron (light_all) as a lighter base 
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap & CartoDB'
            }).addTo(this.map);

            this.markersLayer = L.layerGroup().addTo(this.map);

            this._initialized = true;
            console.log('GuestMapController init complete');

            // Załaduj wydarzenia dla gości
            this.loadGuestEvents();
            
            // Automatyczne odświeżanie co 15 sekund
            this.refreshInterval = setInterval(() => {
                console.log('Auto-refresh: Przeładowywanie wydarzeń...');
                this.loadGuestEvents();
            }, 15000); // 15000ms = 15 sekund
        },

        loadGuestEvents: function(){
            var self = this;

            // Jeśli popup jest otwarty, nie odświeżaj markerów
            if (this.map && this.map._popup && this.map._popup.isOpen && this.map._popup.isOpen()) {
                console.log('Popup otwarty – pomijam odświeżenie markerów');
                return;
            }
            // Najpierw pobierz listę zapisanych wydarzeń
            fetch('/Projekt/public/api/guest-events.php', {
                credentials: 'same-origin'
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.success){
                    self.pendingEvents = data.pending_events || [];
                    console.log('Załadowano zapisane wydarzenia:', self.pendingEvents);
                    self.updateBadge();
                }
                
                // Teraz pobierz wszystkie wydarzenia
                return fetch('/Projekt/public/api/events.php', {
                    credentials: 'same-origin',
                    cache: 'no-cache'
                });
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.success && Array.isArray(data.events)){
                    console.log('Załadowano wydarzenia dla gości:', data.events.length);
                    self.displayEvents(data.events);
                } else {
                    console.error('Błąd danych z API');
                }
            })
            .catch(function(err){
                console.error('Błąd pobierania wydarzeń:', err);
            });
        },

        displayEvents: function(events){
            var self = this;
            
            // Wyczyść istniejące markery
            this.markersLayer.clearLayers();

            // Dodaj markery wydarzeń
            events.forEach(function(event) {
                // Normalizuj event_id (API zwraca 'id', nie 'event_id')
                if(!event.event_id && event.id) event.event_id = event.id;
                
                // Walidacja podstawowych danych wydarzenia
                if(!event.latitude || !event.longitude || !event.event_id) {
                    console.warn('Pominięto wydarzenie - brak wymaganych danych:', event);
                    return;
                }
                if(isNaN(parseFloat(event.latitude)) || isNaN(parseFloat(event.longitude))) {
                    console.warn('Pominięto wydarzenie - nieprawidłowe współrzędne:', event);
                    return;
                }
                
                var marker = L.marker([event.latitude, event.longitude])
                    .bindPopup(self.createPopupHtml(event), {
                        className: 'guest-event-popup',
                        maxWidth: 500,
                        minWidth: 350
                    });
                
                // Po otwarciu popup dodaj handler do przycisku
                marker.on('popupopen', function(){
                    var eventId = parseInt(event.event_id);
                    var btnSave = document.querySelector('.guest-popup-save[data-event-id="' + eventId + '"]');
                    var btnRemove = document.querySelector('.guest-popup-remove[data-event-id="' + eventId + '"]');
                    
                    if(btnSave){
                        btnSave.addEventListener('click', function(){
                            self.handleSaveEvent(eventId);
                        });
                    }
                    
                    if(btnRemove){
                        btnRemove.addEventListener('click', function(){
                            self.handleRemoveEvent(eventId);
                        });
                    }
                });
                
                self.markersLayer.addLayer(marker);
            });
            
            console.log('Wyświetlono markerów:', this.markersLayer.getLayers().length);
        },

        createPopupHtml: function(ev){
            function escapeHtml(text) {
                if(!text) return '';
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Walidacja i sanityzacja event_id - tylko liczby
            var eventId = parseInt(ev.event_id) || 0;
            var creatorText = ev.creator_username || 'Nieznany';
            
            // Sprawdź czy wydarzenie jest już zapisane
            var isSaved = this.pendingEvents.indexOf(eventId) !== -1;
            var buttonHtml = isSaved 
                ? '<button class="btn-guest-remove guest-popup-remove" data-event-id="' + eventId + '">Zrezygnuj</button>'
                : '<button class="btn-guest-save guest-popup-save" data-event-id="' + eventId + '">Dołącz</button>';
            
            var html = 
                '<div class="guest-event-popup">' +
                    '<div class="event-popup-header">' +
                        '<h3 class="event-popup-title">' + escapeHtml(ev.title || 'Bez tytułu') + '</h3>' +
                    '</div>' +
                    '<div class="event-popup-body">' +
                        (ev.category_name ? 
                            '<div class="event-popup-row">' +
                                '<span class="event-popup-icon">🏷️</span>' +
                                '<span class="event-popup-text">' + escapeHtml(ev.category_name) + '</span>' +
                            '</div>' 
                            : '') +
                        '<div class="event-popup-row">' +
                            '<span class="event-popup-icon">📅</span>' +
                            '<span class="event-popup-text">' + escapeHtml(this.formatDateTime(ev.start_datetime)) + '</span>' +
                        '</div>' +
                        (ev.end_datetime ?
                            '<div class="event-popup-row">' +
                                '<span class="event-popup-icon">🏁</span>' +
                                '<span class="event-popup-text">' + escapeHtml(this.formatDateTime(ev.end_datetime)) + '</span>' +
                            '</div>' : '') +
                        '<div class="event-popup-row">' +
                            '<span class="event-popup-icon">👤</span>' +
                            '<span class="event-popup-text">' + escapeHtml(creatorText) + '</span>' +
                        '</div>' +
                        (ev.description ? 
                            '<div class="event-popup-description">' + escapeHtml(ev.description) + '</div>' 
                            : '') +
                    '</div>' +
                    '<div class="event-popup-footer">' +
                        buttonHtml +
                    '</div>' +
                '</div>';
            
            return html;
        },

        formatDateTime: function(dateStr){
            if(!dateStr) return 'Brak daty';
            try {
                var d = new Date(dateStr);
                var day = ('0' + d.getDate()).slice(-2);
                var month = ('0' + (d.getMonth() + 1)).slice(-2);
                var year = d.getFullYear();
                var hours = ('0' + d.getHours()).slice(-2);
                var minutes = ('0' + d.getMinutes()).slice(-2);
                return day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
            } catch(e) {
                return dateStr;
            }
        },

        handleSaveEvent: function(eventId){
            // Walidacja eventId - tylko liczby całkowite dodatnie
            eventId = parseInt(eventId);
            if(!eventId || eventId <= 0 || !Number.isInteger(eventId)){
                console.error('Nieprawidłowe ID wydarzenia:', eventId);
                alert('Błąd: nieprawidłowe ID wydarzenia.');
                return;
            }
            
            console.log('Zapisywanie wydarzenia do sesji:', eventId);
            
            // Wyślij request do API żeby zapisać w sesji
            fetch('/Projekt/public/api/guest-events.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ event_id: eventId })
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.success){
                    console.log('Wydarzenie zapisane w sesji', data.pending_events);
                    
                    // Zaktualizuj lokalną listę
                    this.pendingEvents = data.pending_events || [];
                    
                    // Zaktualizuj badge
                    this.updateBadge();
                    
                    // Zamknij popup i przeładuj markery
                    this.map.closePopup();
                    this.loadGuestEvents();
                    
                    // Pokazz komunikat sukcesu
                    this.showNotification('✓ Wydarzenie zapisane! Zaloguj się aby dołączyć.', 'success');
                } else {
                    this.showNotification('✗ Błąd: ' + (data.message || 'Nie udało się zapisać'), 'error');
                }
            }.bind(this))
            .catch(function(err){
                console.error('Błąd:', err);
                this.showNotification('✗ Błąd połączenia z serwerem', 'error');
            }.bind(this));
        },
        
        handleRemoveEvent: function(eventId){
            eventId = parseInt(eventId);
            if(!eventId || eventId <= 0 || !Number.isInteger(eventId)){
                console.error('Nieprawidłowe ID wydarzenia:', eventId);
                return;
            }
            
            console.log('Usuwanie wydarzenia z sesji:', eventId);
            
            fetch('/Projekt/public/api/guest-events.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ event_id: eventId })
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.success){
                    console.log('Wydarzenie usunięte z sesji', data.pending_events);
                    
                    // Zaktualizuj lokalną listę
                    this.pendingEvents = data.pending_events || [];
                    
                    // Zaktualizuj badge
                    this.updateBadge();
                    
                    // Zamknij popup i przeładuj markery
                    this.map.closePopup();
                    this.loadGuestEvents();
                    
                    this.showNotification('✓ Usunięto z zapisanych wydarzeń', 'success');
                } else {
                    this.showNotification('✗ Błąd usuwania', 'error');
                }
            }.bind(this))
            .catch(function(err){
                console.error('Błąd:', err);
                this.showNotification('✗ Błąd połączenia', 'error');
            }.bind(this));
        },
        
        updateBadge: function(){
            var badge = document.querySelector('.badge-count');
            var count = this.pendingEvents.length;
            
            if(count > 0){
                if(!badge){
                    var button = document.getElementById('yourEventsToggle');
                    if(button){
                        badge = document.createElement('span');
                        badge.className = 'badge-count';
                        button.appendChild(badge);
                    }
                }
                if(badge) badge.textContent = count;
            } else {
                if(badge) badge.remove();
            }
        },
        
        showNotification: function(message, type){
            // Usuń poprzednie notyfikacje
            var existing = document.querySelector('.guest-notification');
            if(existing) existing.remove();
            
            var notification = document.createElement('div');
            notification.className = 'guest-notification guest-notification-' + type;
            notification.textContent = message;
            notification.style.cssText = 
                'position: fixed; top: 7rem; right: 2rem; z-index: 10000; ' +
                'padding: 1rem 1.5rem; border-radius: 0.5rem; ' +
                'font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); ' +
                'animation: slideInRight 0.3s ease; opacity: 0;';
            
            if(type === 'success'){
                notification.style.background = 'linear-gradient(135deg, #16d10596 0%, #4ba259 100%)';
                notification.style.color = 'white';
            } else {
                notification.style.background = '#ff4444';
                notification.style.color = 'white';
            }
            
            document.body.appendChild(notification);
            
            // Animacja wejścia
            setTimeout(function(){ notification.style.opacity = '1'; }, 10);
            
            // Automatyczne usunięcie po 3 sekundach
            setTimeout(function(){
                notification.style.transition = 'opacity 0.3s';
                notification.style.opacity = '0';
                setTimeout(function(){ notification.remove(); }, 300);
            }, 3000);
        }
    };

    // Auto-init po załadowaniu DOM
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('guest-map')) {
            window.GuestMapController.init();
        }
    });
})();