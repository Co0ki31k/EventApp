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

        init: function(){
            if(this._initialized) return;
            if(typeof L === 'undefined') return;
            if(!window.UserMapController || !window.UserMapController.map) return;

            var map = window.UserMapController.map;
            // reuse controller's layer if present, otherwise create our own and save back
            this.markersLayer = window.UserMapController.markersLayer || L.layerGroup().addTo(map);
            window.UserMapController.markersLayer = this.markersLayer;

            this._initialized = true;

            if(this.autoLoadOnInit){
                try{ this.loadFromApi(); } catch(e){ console.error(e); }
            }
        },

        // Add a single marker for an event object
        addMarkerForEvent: function(ev){
            if(!ev || ev.latitude == null || ev.longitude == null) return null;
            this.init();
            if(!this.markersLayer) return null;

            var lat = parseFloat(ev.latitude);
            var lng = parseFloat(ev.longitude);
            if(Number.isNaN(lat) || Number.isNaN(lng)) return null;

            // Determine marker icon based on creator role:
            // - Blue icon for events created by regular users
            // - Red icon for events created by companies
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

            var title = ev.title || '';
            var start = ev.start_datetime || '';
            var descr = ev.description || '';
            var creatorInfo = ev.creator_username ? (' • ' + escapeHtml(ev.creator_username)) : '';
            var roleLabel = (ev.creator_role === 'company') ? ' (Firma)' : '';
            var popup = '<div class="map-popup"><strong>' + escapeHtml(title) + '</strong>' +
                        (start ? ('<div class="muted">' + escapeHtml(start) + creatorInfo + roleLabel + '</div>') : '') +
                        (descr ? ('<div>' + escapeHtml(descr) + '</div>') : '') +
                        '</div>';

            marker.bindPopup(popup);
            marker.addTo(this.markersLayer);
            marker._eventId = ev.id || null;
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
                    data.events.forEach(function(ev){ self.addMarkerForEvent(ev); });
                    console.log('UserMapMarkers: loaded', self.markers.length, 'markers');
                    return self.markers;
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
        try{ window.UserMapMarkers.reload(); } catch(e){ console.error(e); }
    });

    // Auto-reload markers every 10 seconds to see events from other users
    setInterval(function(){
        if(window.UserMapMarkers && window.UserMapMarkers._initialized){
            try{ window.UserMapMarkers.reload(); } catch(e){ console.error(e); }
        }
    }, 10000);

})();
