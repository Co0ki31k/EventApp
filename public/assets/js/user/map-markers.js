/* map-markers.js
   Separate module for loading and managing map markers (pins).
   Depends on Leaflet and `window.UserMapController` provided by map-controller.js
*/
(function(){
    window.UserMapMarkers = {
        markersLayer: null,
        markers: [],
        _initialized: false,
        autoLoadOnInit: true,
        currentUserId: null, 
        participationCache: {},

        init: function(){
            if(this._initialized) return;
            if(typeof L === 'undefined') return;
            if(!window.UserMapController || !window.UserMapController.map) return;

            var map = window.UserMapController.map;
            // reuse controller's layer if present, otherwise create our own and save back
            this.markersLayer = window.UserMapController.markersLayer || L.layerGroup().addTo(map);
            window.UserMapController.markersLayer = this.markersLayer;

            // Try to get current user ID from global variable (set in PHP)
            if(typeof window.currentUserId !== 'undefined'){
                this.currentUserId = window.currentUserId;
            }

            this._initialized = true;

            if(this.autoLoadOnInit){
                try{ this.loadFromApi(); } catch(e){ console.error(e); }
            }
        },

        // Check if current user is participant of an event
        checkParticipation: function(eventId){
            var self = this;
            if(!this.currentUserId) return Promise.resolve(false);
            
            return fetch('/Projekt/public/api/participants.php?event_id=' + eventId + '&user_id=' + this.currentUserId, {
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                var isJoined = data.success && data.is_joined;
                self.participationCache[eventId] = isJoined;
                return isJoined;
            })
            .catch(function(){ return false; });
        },

        // Join event via API
        joinEvent: function(eventId){
            var self = this;
            return fetch('/Projekt/public/api/participants.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: parseInt(eventId) })
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.success){
                    self.participationCache[eventId] = true;
                }
                return data;
            });
        },

        // Leave event via API
        leaveEvent: function(eventId){
            var self = this;
            return fetch('/Projekt/public/api/participants.php', {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: parseInt(eventId) })
            })
            .then(function(res){ return res.json(); })
            .then(function(data){
                if(data.success){
                    self.participationCache[eventId] = false;
                }
                return data;
            });
        },

        // Format datetime for display
        formatDateTime: function(dateStr){
            if(!dateStr) return 'Nie podano';
            try {
                var date = new Date(dateStr);
                if(isNaN(date.getTime())) return dateStr;
                return date.toLocaleDateString('pl-PL', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch(e){
                return dateStr;
            }
        },

        // Build popup HTML content for event
        buildPopupContent: function(ev, isJoined){
            var isCompany = ev.creator_role === 'company';
            var badgeClass = isCompany ? 'badge-company' : 'badge-user';
            var badgeText = isCompany ? 'Firma' : 'Użytkownik';
            var creatorText = ev.creator_username || 'Nieznany';
            
            // Determine button state
            var btnClass = isJoined ? 'btn-popup-leave' : 'btn-popup-join';
            var btnText = isJoined ? 'Rezygnuj' : 'Dołącz';
            var btnDataJoined = isJoined ? 'true' : 'false';
            
            // Check if user is the creator (can't join own event)
            var isOwner = this.currentUserId && ev.created_by === this.currentUserId;
            
            var html = 
                '<div class="event-popup">' +
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
                            '<span class="event-popup-text">' + this.formatDateTime(ev.start_datetime) + '</span>' +
                        '</div>' +
                        '<div class="event-popup-row">' +
                            '<span class="event-popup-icon">🏁</span>' +
                            '<span class="event-popup-text">' + this.formatDateTime(ev.end_datetime) + '</span>' +
                        '</div>' +
                        '<div class="event-popup-row">' +
                            '<span class="event-popup-icon">👤</span>' +
                            '<span class="event-popup-text">' + escapeHtml(creatorText) + '</span>' +
                        '</div>' +
                        (ev.description ? 
                            '<div class="event-popup-description">' + escapeHtml(ev.description) + '</div>' 
                            : '') +
                    '</div>' +
                    '<div class="event-popup-footer">' +
                        (isOwner ? 
                            '<span class="event-popup-owner-badge">Twoje wydarzenie</span>' :
                            '<button class="btn-popup-action ' + btnClass + '" data-event-id="' + (ev.id || '') + '" data-joined="' + btnDataJoined + '">' + btnText + '</button>'
                        ) +
                    '</div>' +
                '</div>';
            
            return html;
        },

        // Update button state in popup
        updatePopupButton: function(eventId, isJoined){
            var btn = document.querySelector('.btn-popup-action[data-event-id="' + eventId + '"]');
            if(!btn) return;
            
            btn.setAttribute('data-joined', isJoined ? 'true' : 'false');
            btn.classList.remove('btn-popup-join', 'btn-popup-leave');
            btn.classList.add(isJoined ? 'btn-popup-leave' : 'btn-popup-join');
            btn.textContent = isJoined ? 'Rezygnuj' : 'Dołącz';
        },

        // Add a single marker for an event object
        addMarkerForEvent: function(ev, isJoined){
            if(!ev || ev.latitude == null || ev.longitude == null) return null;
            this.init();
            if(!this.markersLayer) return null;

            var lat = parseFloat(ev.latitude);
            var lng = parseFloat(ev.longitude);
            if(Number.isNaN(lat) || Number.isNaN(lng)) return null;

            var markerIcon = null;
            if(window.MapIcons){
                var creatorRole = ev.creator_role || 'user';
                if(creatorRole === 'company'){
                    markerIcon = window.MapIcons.getRedIcon();
                } else {
                    markerIcon = window.MapIcons.getBlueIcon();
                }
            }

            var marker = L.marker([lat, lng], { icon: markerIcon });

            // Store event data on marker for later use
            marker._eventData = ev;
            marker._eventId = ev.id || null;
            marker._isJoined = isJoined || false;

            // Create Leaflet popup with custom content
            var popupContent = this.buildPopupContent(ev, isJoined);
            var popup = L.popup({
                className: 'event-leaflet-popup'
            }).setContent(popupContent);

            marker.bindPopup(popup);

            // Bind action button event after popup opens
            var self = this;
            var eventId = ev.id; // Store event ID for closure
            marker.on('popupopen', function(){
                
                // Bind action button
                var btn = document.querySelector('.btn-popup-action[data-event-id="' + eventId + '"]');
                if(btn){
                    btn.addEventListener('click', function(){
                        var isCurrentlyJoined = this.getAttribute('data-joined') === 'true';
                        var btnEl = this;
                        
                        // Disable button during request
                        btnEl.disabled = true;
                        btnEl.textContent = 'Ładowanie...';
                        
                        if(isCurrentlyJoined){
                            // Leave event
                            self.leaveEvent(eventId)
                                .then(function(data){
                                    if(data.success){
                                        self.updatePopupButton(eventId, false);
                                    } else {
                                        alert(data.message || 'Błąd podczas rezygnacji');
                                        self.updatePopupButton(eventId, true);
                                    }
                                })
                                .catch(function(){
                                    alert('Błąd połączenia');
                                    self.updatePopupButton(eventId, true);
                                })
                                .finally(function(){
                                    btnEl.disabled = false;
                                });
                        } else {
                            // Join event
                            self.joinEvent(eventId)
                                .then(function(data){
                                    if(data.success){
                                        self.updatePopupButton(eventId, true);
                                    } else {
                                        alert(data.message || 'Błąd podczas dołączania');
                                        self.updatePopupButton(eventId, false);
                                    }
                                })
                                .catch(function(){
                                    alert('Błąd połączenia');
                                    self.updatePopupButton(eventId, false);
                                })
                                .finally(function(){
                                    btnEl.disabled = false;
                                });
                        }
                    });
                }
            });

            marker.addTo(this.markersLayer);
            this.markers.push(marker);
            return marker;
        },

        // Load events from API and place markers
        loadFromApi: function(url){
            url = url || '/Projekt/public/api/events.php';
            this.init();
            var self = this;
            
            return fetch(url, { credentials: 'same-origin', cache: 'no-cache' })
                .then(function(res){
                    if(!res.ok) throw new Error('Network response not ok');
                    return res.json();
                })
                .then(function(data){
                    if(!data || !data.success || !Array.isArray(data.events)) return [];
                    self.clear();
                    
                    // Check participation for all events (session-based on server)
                    var participationPromises = data.events.map(function(ev){
                        return self.checkParticipation(ev.id);
                    });
                    
                    return Promise.all(participationPromises).then(function(participationResults){
                        data.events.forEach(function(ev, index){
                            var isJoined = participationResults[index];
                            self.addMarkerForEvent(ev, isJoined);
                        });
                        console.log('UserMapMarkers: loaded', self.markers.length, 'markers');
                        return self.markers;
                    });
                })
                .catch(function(err){
                    console.error('UserMapMarkers.loadFromApi error', err);
                    return [];
                });
        },

        // Reload markers (convenience alias)
        reload: function(){
            return this.loadFromApi();
        },

        // Clear all event markers
        clear: function(){
            if(!this.markersLayer) return;
            var self = this;
            this.markers.forEach(function(m){
                try{ self.markersLayer.removeLayer(m); } catch(e){}
            });
            this.markers = [];
        },

        // Get marker by event id
        getMarkerByEventId: function(id){
            for(var i = 0; i < this.markers.length; i++){
                if(this.markers[i]._eventId === id) return this.markers[i];
            }
            return null;
        },

        // Highlight / focus on a marker
        focusMarker: function(marker){
            if(!marker || !window.UserMapController || !window.UserMapController.map) return;
            var map = window.UserMapController.map;
            map.setView(marker.getLatLng(), Math.max(map.getZoom(), 15));
            marker.openPopup();
        }
    };

    // HTML escape helper
    function escapeHtml(str){
        if(!str) return '';
        return String(str).replace(/[&<>"']/g, function(s){
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s];
        });
    }

    // Listen for map ready event from map-controller.js
    window.addEventListener('map:ready', function(){
        console.log('UserMapMarkers: received map:ready event');
        try{ window.UserMapMarkers.init(); } catch(e){ console.error(e); }
    });


    // Listen for event creation to reload markers
    window.addEventListener('event:created', function(){
        // Skip reload if popup is open
        var hasOpenPopup = document.querySelector('.leaflet-popup-pane .leaflet-popup') !== null;
        if(!hasOpenPopup){
            try{ window.UserMapMarkers.reload(); } catch(e){ console.error(e); }
        }
    });

    // Auto-reload markers every 10 seconds to see events from other users
    // Skip reload if any popup is open
    setInterval(function(){
        if(window.UserMapMarkers && window.UserMapMarkers._initialized){
            // Check if any popup is open
            var hasOpenPopup = document.querySelector('.leaflet-popup-pane .leaflet-popup') !== null;
            if(!hasOpenPopup){
                try{ window.UserMapMarkers.reload(); } catch(e){ console.error(e); }
            }
        }
    }, 10000);

})();
